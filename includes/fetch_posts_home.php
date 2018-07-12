<?php
session_start();
include("../config/connect.php");
include("fetch_users_info.php");
include ("time_function.php");
include ("num_k_m_count.php");
$s_id = $_SESSION['id'];
$check_path = filter_var(htmlspecialchars($_POST['path']),FILTER_SANITIZE_STRING);
$plimit = filter_var(htmlspecialchars($_POST['plimit']),FILTER_SANITIZE_NUMBER_INT);
$p_privacy = "2";
$vpsql = "SELECT * FROM wpost WHERE author_id IN 
(SELECT uf_two FROM follow WHERE uf_one=:s_id) AND p_privacy != :p_privacy OR author_id=:s_id ORDER BY post_time DESC LIMIT :plimit,10";
$view_posts = $conn->prepare($vpsql);
$view_posts->bindValue(':s_id', $s_id, PDO::PARAM_INT);
$view_posts->bindValue(':p_privacy', $p_privacy, PDO::PARAM_INT);
$view_posts->bindValue(':plimit', (int)trim($plimit), PDO::PARAM_INT);
$view_posts->execute();
$view_postsNum = $view_posts->rowCount();
if ($view_postsNum > 0) {
	include "fetch_posts.php";
}else{
	echo "0";
}
?>