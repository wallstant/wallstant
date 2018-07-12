<?php
include("../config/connect.php");
session_start();
$uid = htmlentities($_POST['uid'], ENT_QUOTES);
$pid = htmlentities($_POST['pid'], ENT_QUOTES);

$sql = "SELECT id FROM r_star WHERE u_id = :uid AND p_id =:pid";
$starCheck = $conn->prepare($sql);
$starCheck->bindParam(':uid',$uid,PDO::PARAM_INT);
$starCheck->bindParam(':pid',$pid,PDO::PARAM_INT);
$starCheck->execute();
$starCheckExist = $starCheck->rowCount();
if ($starCheckExist > 0) {
	$add_sql = "DELETE FROM r_star WHERE u_id = :uid AND p_id = :pid";
	$add_star = $conn->prepare($add_sql);
	$add_star->bindParam(':uid',$uid,PDO::PARAM_INT);
	$add_star->bindParam(':pid',$pid,PDO::PARAM_INT);
	$add_star->execute();
	echo "<button class='follow_btn' onclick='starPage(\"$uid\",\"$pid\")' style='width:100%;margin:0px 3px;padding:10px 15px;' title='".lang('star')."'><span class='fa fa-star-o' style='color:#bbbbbb;font-size:18px;'></span></button>";
	// Delete notification to user
    $s_id = $_SESSION['id'];
    $notifyType = "star";
    $sendNotification = $conn->prepare("DELETE FROM notifications WHERE from_id =:from_id AND for_id=:for_id AND notifyType_id=:ntId AND notifyType=:notifyType");
    $sendNotification->bindParam(':from_id',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':for_id',$pid,PDO::PARAM_INT);
    $sendNotification->bindParam(':ntId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
    $sendNotification->execute();
    // ==================================
}else{
	$add_sql = "INSERT INTO r_star (u_id,p_id) VALUES (:uid,:pid)";
	$add_star = $conn->prepare($add_sql);
	$add_star->bindParam(':uid',$uid,PDO::PARAM_INT);
	$add_star->bindParam(':pid',$pid,PDO::PARAM_INT);
	$add_star->execute();
	echo "<button class='follow_btn' onclick='starPage(\"$uid\",\"$pid\")' style='width:100%;margin:0px 3px;border-color:#ffc107;padding:10px 15px;' title='".lang('unstar')."'><span class='fa fa-star' style='color:#FFC107;font-size:18px;'></span></button>";
	// send notification to user
    $nId = rand(0,999999999)+time();
    $s_id = $_SESSION['id'];
    $notifyType = "star";
    $nSeen = "0";
    $nTime = time();
    if ($pid != $s_id) {
    $sendNotification = $conn->prepare("INSERT INTO notifications (n_id, from_id, for_id, notifyType_id, notifyType, seen, time) VALUES (:nId, :fromId, :forId, :notifyTypeId, :notifyType, :seen, :nTime)");
    $sendNotification->bindParam(':nId',$nId,PDO::PARAM_INT);
    $sendNotification->bindParam(':fromId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':forId',$pid,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyTypeId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
    $sendNotification->bindParam(':seen',$nSeen,PDO::PARAM_INT);
    $sendNotification->bindParam(':nTime',$nTime,PDO::PARAM_INT);
    $sendNotification->execute();
    }
    // ==================================
}
exit();
?>