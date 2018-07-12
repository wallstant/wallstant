<?php
session_start();
include("../config/connect.php");
include("time_function.php");
$comment_id = htmlentities($_POST['cid'], ENT_QUOTES);
$edit_commant_var = htmlentities($_POST['cContent'], ENT_QUOTES);
$check_path_var = htmlentities($_POST['cp'], ENT_QUOTES);
$timeEdited = time();
$edited = "1";

$commentEdit_sql = "UPDATE comments SET c_content= :edit_commant_var , c_edited= :edited , c_time_edited= :timeEdited WHERE c_id= :comment_id";
$commentEdit = $conn->prepare($commentEdit_sql);
$commentEdit->bindParam(':edit_commant_var',$edit_commant_var,PDO::PARAM_STR);
$commentEdit->bindParam(':edited',$edited,PDO::PARAM_INT);
$commentEdit->bindParam(':timeEdited',$timeEdited,PDO::PARAM_STR);
$commentEdit->bindParam(':comment_id',$comment_id,PDO::PARAM_INT);
$commentEdit->execute();

$em_img_path = $check_path_var."imgs/emoticons/";
include ("emoticons.php");
$comm_body = str_replace($em_char,$em_img,$edit_commant_var);
$hashtag_path = $check_path_var."hashtag/";
$hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$comm_body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $comm_body);
$comm_body = nl2br($comm_body);
$timeEdited = time_ago(time());
$results = array();
$results[0] = $timeEdited;
$results[1] = $comm_body;
echo json_encode($results);
exit();
?>