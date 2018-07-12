<?php
session_start();
include("../config/connect.php");
session_start();
$pid = filter_var(htmlspecialchars($_POST['pid']),FILTER_SANITIZE_NUMBER_INT);
$myid = $_SESSION['id'];
$time = time();
$path = filter_var(htmlspecialchars($_POST['path']),FILTER_SANITIZE_STRING);
$p_id = time()+rand(0,9999999);
$p_privacy = "0";

$createPost = $conn->prepare("INSERT INTO wpost 
	(post_id,author_id,post_time,p_privacy,shared) VALUES
	(:post_id, :p_author_id,:p_time, :p_privacy, :shared)");
$createPost->bindParam(':post_id',$p_id,PDO::PARAM_INT);
$createPost->bindParam(':p_author_id',$myid,PDO::PARAM_INT);
$createPost->bindParam(':p_time',$time,PDO::PARAM_INT);
$createPost->bindParam(':p_privacy',$p_privacy,PDO::PARAM_INT);
$createPost->bindParam(':shared',$pid,PDO::PARAM_STR);
$createPost->execute();
// send notification to user
$get_post_authorId = $conn->prepare("SELECT author_id FROM wpost WHERE post_id=:pid");
$get_post_authorId->bindParam(':pid',$pid,PDO::PARAM_INT);
$get_post_authorId->execute();
while ($getAuthor = $get_post_authorId->fetch(PDO::FETCH_ASSOC)) {
$nId = rand(0,999999999)+time();
$for_id = $getAuthor['author_id'];
$notifyType = "share";
$nSeen = "0";
$nTime = time();
if ($for_id != $myid) {
$sendNotification = $conn->prepare("INSERT INTO notifications (n_id, from_id, for_id, notifyType_id, notifyType, seen, time) VALUES (:nId, :fromId, :forId, :notifyTypeId, :notifyType, :seen, :nTime)");
$sendNotification->bindParam(':nId',$nId,PDO::PARAM_INT);
$sendNotification->bindParam(':fromId',$myid,PDO::PARAM_INT);
$sendNotification->bindParam(':forId',$for_id,PDO::PARAM_INT);
$sendNotification->bindParam(':notifyTypeId',$p_id,PDO::PARAM_INT);
$sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
$sendNotification->bindParam(':seen',$nSeen,PDO::PARAM_INT);
$sendNotification->bindParam(':nTime',$nTime,PDO::PARAM_INT);
$sendNotification->execute();
}
}
// ==================================
if ($createPost) {
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid #2196F3;'>
	<span class='fa fa-times' onclick=\"canselPostNotify2('".$pid."')\"></span> ".lang('postShared')."
	</p>
	";
}else {
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid red;'>
	<span class='fa fa-times' onclick=\"canselPostNotify2('".$pid."')\"></span> ".lang('errorSomthingWrong')."
	</p>
	";
}
?>