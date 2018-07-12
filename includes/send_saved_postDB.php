<?php
session_start();
include("../config/connect.php");
$pid = filter_var(htmlspecialchars($_POST['pid']),FILTER_SANITIZE_NUMBER_INT);
$myid = filter_var(htmlspecialchars($_POST['myid']),FILTER_SANITIZE_NUMBER_INT);
$st = time();
$path = filter_var(htmlspecialchars($_POST['path']),FILTER_SANITIZE_STRING);
$id = rand(0,9999999)+time();
$checkExist_sql = "SELECT * FROM saved WHERE post_id= :pid AND user_saved_id= :myid";
$checkExist = $conn->prepare($checkExist_sql);
$checkExist->bindParam(':pid',$pid,PDO::PARAM_INT);
$checkExist->bindParam(':myid',$myid,PDO::PARAM_INT);
$checkExist->execute();
$checkExist_count = $checkExist->rowCount();
if ($checkExist_count < 1) {
	$save_post_sql = "INSERT INTO saved (id, post_id, user_saved_id, saved_time) VALUES (:id, :pid, :myid, :st)";
	$save_post = $conn->prepare($save_post_sql);
	$save_post->bindParam(':id',$id,PDO::PARAM_INT);
	$save_post->bindParam(':pid',$pid,PDO::PARAM_INT);
	$save_post->bindParam(':myid',$myid,PDO::PARAM_INT);
	$save_post->bindParam(':st',$st,PDO::PARAM_STR);
	$save_post->execute();
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid #2196F3;'>
	<span class='fa fa-times' onclick=\"canselPostNotify('".$pid."')\"></span> ".lang('postSaved').", <a href='".$path."posts/saved'>".lang('saved')."</a>.
	</p>
	";
}else{
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid red;'>
	<span class='fa fa-times' onclick=\"canselPostNotify('".$pid."')\"></span> ".lang('postSavedBefore')."
	</p>
	";
}
?>