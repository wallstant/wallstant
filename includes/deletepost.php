<?php
include("../config/connect.php");
session_start();
$sid = $_SESSION['id'];
$posts_id = filter_var(htmlspecialchars($_POST['pid']),FILTER_SANITIZE_NUMBER_INT);
$check = $conn->prepare("SELECT author_id FROM wpost WHERE post_id =:posts_id");
$check->bindParam(':posts_id',$posts_id,PDO::PARAM_INT);
$check->execute();
while ($chR = $check->fetch(PDO::FETCH_ASSOC)) {
	$chR_aid = $chR['author_id'];
}
if ($chR_aid == $sid) {
	$delete_post_sql = "DELETE FROM wpost WHERE post_id= :posts_id";
	$delete_post = $conn->prepare($delete_post_sql);
	$delete_post->bindParam(':posts_id',$posts_id,PDO::PARAM_INT);
	$delete_post->execute();
	// Delete notification 'like'
	$notifyType = "like";
	$sendNotification = $conn->prepare("DELETE FROM notifications WHERE notifyType_id=:notifyType_id AND notifyType=:notifyType");
	$sendNotification->bindParam(':notifyType_id',$posts_id,PDO::PARAM_INT);
	$sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
	$sendNotification->execute();
	// Delete notification 'comment'
	$notifyType = "comment";
	$sendNotification = $conn->prepare("DELETE FROM notifications WHERE notifyType_id=:notifyType_id AND notifyType=:notifyType");
	$sendNotification->bindParam(':notifyType_id',$posts_id,PDO::PARAM_INT);
	$sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
	$sendNotification->execute();
	echo "yes";
}else{
	echo "no";
}
?>