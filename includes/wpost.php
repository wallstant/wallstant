<?php
session_start();
include "../config/connect.php";
$post_id = rand(0,9999999)+time();
$p_author_id = $_SESSION['id'];
$p_author = $_SESSION['Fullname'];
$p_author_photo = $_SESSION['Userphoto'];
$p_time = time();
$p_img = $_FILES["w_photo"]["name"];
$p_content = filter_var(htmlspecialchars($_POST['post_textbox']),FILTER_SANITIZE_STRING);
$p_title = filter_var(htmlspecialchars($_POST['w_title']), FILTER_SANITIZE_STRING);
$check_path = filter_var(htmlspecialchars($_POST['check_path']), FILTER_SANITIZE_STRING);
switch (filter_var(htmlspecialchars($_POST['w_privacy']),FILTER_SANITIZE_STRING)) {
case lang('wpr_public'):
    $p_privacy = "0";
    break;
case lang('wpr_followers'):
    $p_privacy = "1";
    break;
case lang('wpr_onlyme'):
    $p_privacy = "2";
    break;
}
    //================[ if image not empty ]================
if (!empty($p_img)) {
$post_fileName = $_FILES["w_photo"]["name"];
$post_fileTmpLoc = $_FILES["w_photo"]["tmp_name"];
$post_fileType = $_FILES["w_photo"]["type"];
$post_fileSize = $_FILES["w_photo"]["size"]; 
$post_fileErrorMsg = $_FILES["w_photo"]["error"];
$post_fileName = preg_replace('#[^a-z.0-9]#i', '', $post_fileName); 
$post_kaboom = explode(".", $post_fileName);
$post_fileExt = end($post_kaboom);
$post_fileName = time().rand().".".$post_fileExt;
    //================[ if not selected an image ]================
if (!$post_fileTmpLoc) {
echo "
<p id='error_msg' onclick='hideMsg()'>
".lang('errorPost_n2')."
</p>
";
}else{
    //================[ if image size more than 4Mb ]================
     if($post_fileSize > 4242880) {
    echo "
<p id='error_msg' onclick='hideMsg()'>
".lang('errorPost_n3')."
</p>
";
unlink($post_fileTmpLoc); 
} else {
    //================[ if image format not supported ]================
if (!preg_match("/.(jpeg|jpg|png)$/i", $post_fileName) ) { 
echo "
<p id='error_msg' onclick='hideMsg()'>
".lang('errorPost_n4')."
</p>
"; 
     unlink($post_fileTmpLoc);
} else {
    //================[ if an error was found ]================
if ($post_fileErrorMsg == 1) {
    echo "
<p id='error_msg' onclick='hideMsg()'>
".lang('errorPost_n5')."
</p>
";
}else{
    //================[ check image path ]================
if (is_dir("../imgs/user_post_img/")) {
    $imgsPath = "../imgs/user_post_img/";
}else{
    $imgsPath = "imgs/user_post_img/";
}
$post_moveResult = move_uploaded_file($post_fileTmpLoc, $imgsPath."$post_fileName");
    //================[ if image uploaded successfuly ]================
if ($post_moveResult != true) {
    echo "
<p id='error_msg' onclick='hideMsg()'>
".lang('errorPost_n6')."
</p>
";
}else{
    $p_img = "user_post_img/".$post_fileName;
}

$iptdbsql = "INSERT INTO wpost
(post_id,author_id,post_img,post_time,post_content,p_title,p_privacy)
VALUES
( :post_id, :p_author_id, :p_img, :p_time, :p_content, :p_title, :p_privacy)
";
$insert_post_toDB = $conn->prepare($iptdbsql);
$insert_post_toDB->bindParam(':post_id', $post_id,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_author_id', $p_author_id,PDO::PARAM_INT);
$insert_post_toDB->bindParam(':p_img', $p_img,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_time', $p_time,PDO::PARAM_INT);
$insert_post_toDB->bindParam(':p_content', $p_content,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_title', $p_title,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_privacy', $p_privacy,PDO::PARAM_STR);
$insert_post_toDB->execute();
}
}
}
}
}else{
$iptdbsql = "INSERT INTO wpost
(post_id,author_id,post_time,post_content,p_title,p_privacy)
VALUES
( :post_id, :p_author_id, :p_time, :p_content, :p_title, :p_privacy)
";
$insert_post_toDB = $conn->prepare($iptdbsql);
$insert_post_toDB->bindParam(':post_id', $post_id,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_author_id', $p_author_id,PDO::PARAM_INT);
$insert_post_toDB->bindParam(':p_time', $p_time,PDO::PARAM_INT);
$insert_post_toDB->bindParam(':p_content', $p_content,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_title', $p_title,PDO::PARAM_STR);
$insert_post_toDB->bindParam(':p_privacy', $p_privacy,PDO::PARAM_STR);
$insert_post_toDB->execute();
}
include("fetch_users_info.php");
include ("time_function.php");
include ("num_k_m_count.php");
$vpsql = "SELECT * FROM wpost WHERE post_id = :post_id";
$view_posts = $conn->prepare($vpsql);
$view_posts->bindParam(':post_id', $post_id, PDO::PARAM_INT);
$view_posts->execute();
include "fetch_posts.php";
?>