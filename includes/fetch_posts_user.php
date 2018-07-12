<?php
session_start();
include("../config/connect.php");
include("fetch_users_info.php");
include ("time_function.php");
include ("num_k_m_count.php");
$check_path = filter_var(htmlspecialchars($_POST['path']),FILTER_SANITIZE_STRING);
$plimit = filter_var(htmlspecialchars($_POST['plimit']),FILTER_SANITIZE_NUMBER_INT);
$row_id = filter_var(htmlspecialchars($_POST['rid']),FILTER_SANITIZE_NUMBER_INT);
$pr_onlyme = "2";
$pr_followers = "1";
$s_id = $_SESSION['id'];
$user_id = $row_id;
$checkIfollowedHim_sql = "SELECT * FROM follow WHERE uf_one=:s_id AND uf_two=:user_id";
$checkIfollowedHim = $conn->prepare($checkIfollowedHim_sql);
$checkIfollowedHim->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$checkIfollowedHim->bindParam(':user_id',$user_id,PDO::PARAM_INT);
$checkIfollowedHim->execute();
$checkIfollowedHimCount = $checkIfollowedHim->rowCount();
if ($_SESSION['id'] != $row_id) {
if ($checkIfollowedHimCount > 0) {
$vpssql = "SELECT * FROM wpost WHERE author_id=:row_id AND p_privacy != :pr_onlyme  ORDER BY post_time DESC LIMIT :plimit,10";
$view_posts = $conn->prepare($vpssql);
$view_posts->bindValue(':row_id', $row_id, PDO::PARAM_INT);
$view_posts->bindValue(':pr_onlyme', $pr_onlyme, PDO::PARAM_INT);
$view_posts->bindValue(':plimit', (int)trim($plimit), PDO::PARAM_INT);
$view_posts->execute();
}elseif ($checkIfollowedHimCount < 1){
$vpssql = "SELECT * FROM wpost WHERE author_id=:row_id AND p_privacy != :pr_onlyme AND p_privacy != :pr_followers ORDER BY post_time DESC LIMIT :plimit,10";
$view_posts = $conn->prepare($vpssql);
$view_posts->bindValue(':row_id', $row_id, PDO::PARAM_INT);
$view_posts->bindValue(':pr_onlyme', $pr_onlyme, PDO::PARAM_INT);
$view_posts->bindValue(':pr_followers', $pr_followers, PDO::PARAM_INT);
$view_posts->bindValue(':plimit', (int)trim($plimit), PDO::PARAM_INT);
$view_posts->execute();
}
}else{
$vpssql = "SELECT * FROM wpost WHERE author_id=:row_id ORDER BY post_time DESC LIMIT :plimit,10";
$view_posts = $conn->prepare($vpssql);
$view_posts->bindValue(':row_id', $row_id, PDO::PARAM_INT);
$view_posts->bindValue(':plimit', (int)trim($plimit), PDO::PARAM_INT);
$view_posts->execute();
}
$view_postsNum = $view_posts->rowCount();
if ($view_postsNum > 0) {
	include "fetch_posts.php";
}else{
	echo "0";
}
?>
