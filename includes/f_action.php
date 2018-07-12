<?php
session_start();
include("../config/connect.php");
$s_id = $_SESSION['id'];
$user_id = filter_var(htmlentities($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
$fid = "";
$checkfollow_sql = "SELECT * FROM follow WHERE uf_one=:s_id AND uf_two=:user_id";
    $checkfollow = $conn->prepare($checkfollow_sql);
    $checkfollow->bindParam(':s_id',$s_id,PDO::PARAM_INT);
    $checkfollow->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $checkfollow->execute();
    $fchecknum = $checkfollow->rowCount();
if ($fchecknum > 0) {
    // unfollow user [AQL Query]
    $unfollow_sql = "DELETE FROM follow WHERE uf_one=:s_id AND uf_two=:user_id";
    $unfollow = $conn->prepare($unfollow_sql);
    $unfollow->bindParam(':s_id',$s_id,PDO::PARAM_INT);
    $unfollow->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $unfollow->execute();
    $followbtn = "<button class=\"follow_btn\" onclick=\"followUnfollow('$user_id')\"><span class=\"fa fa-plus-circle\"></span> ".lang('followBtn_str')."</button>";

    // update following number
    $followers_sql = "SELECT id FROM follow WHERE uf_two=:user_id";
    $followers = $conn->prepare($followers_sql);
    $followers->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $followers->execute();
    $followers_num = $followers->rowCount();
    $UpdateFollowersSql = "UPDATE signup SET followers=:followers_num WHERE id=:user_id";
    $UpdateFollowers = $conn->prepare($UpdateFollowersSql);
    $UpdateFollowers->bindParam(':followers_num',$followers_num,PDO::PARAM_INT);
    $UpdateFollowers->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $UpdateFollowers->execute();

    // Delete notification to user
    $s_id = $_SESSION['id'];
    $notifyType = "follow";
    $sendNotification = $conn->prepare("DELETE FROM notifications WHERE from_id =:from_id AND for_id=:for_id AND notifyType_id=:ntId AND notifyType=:notifyType");
    $sendNotification->bindParam(':from_id',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':for_id',$user_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':ntId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
    $sendNotification->execute();
    // ==================================
}else{
    // follow user [AQL Query]
    $follow_sql = "INSERT INTO follow VALUES (:fid,:s_id,:user_id)";
    $follow = $conn->prepare($follow_sql);
    $follow->bindParam(':fid',$fid,PDO::PARAM_INT);
    $follow->bindParam(':s_id',$s_id,PDO::PARAM_INT);
    $follow->bindParam(':user_id',$user_id,PDO::PARAM_STR);
    $follow->execute();
    $followbtn = "<button class=\"unfollow_btn\" onclick=\"followUnfollow('$user_id')\"><span class=\"fa fa-check\"></span> ".lang('followingBtn_str')."</button>";

    // update followers number
    $followers_sql = "SELECT id FROM follow WHERE uf_two=:user_id";
    $followers = $conn->prepare($followers_sql);
    $followers->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $followers->execute();
    $followers_num = $followers->rowCount();
    $UpdateFollowersSql = "UPDATE signup SET followers=:followers_num WHERE id=:user_id";
    $UpdateFollowers = $conn->prepare($UpdateFollowersSql);
    $UpdateFollowers->bindParam(':followers_num',$followers_num,PDO::PARAM_INT);
    $UpdateFollowers->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $UpdateFollowers->execute();

    // send notification to user
    $nId = rand(0,999999999)+time();
    $s_id = $_SESSION['id'];
    $notifyType = "follow";
    $nSeen = "0";
    $nTime = time();
    if ($user_id != $s_id) {
    $sendNotification = $conn->prepare("INSERT INTO notifications (n_id, from_id, for_id, notifyType_id, notifyType, seen, time) VALUES (:nId, :fromId, :forId, :notifyTypeId, :notifyType, :seen, :nTime)");
    $sendNotification->bindParam(':nId',$nId,PDO::PARAM_INT);
    $sendNotification->bindParam(':fromId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':forId',$user_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyTypeId',$s_id,PDO::PARAM_INT);
    $sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
    $sendNotification->bindParam(':seen',$nSeen,PDO::PARAM_INT);
    $sendNotification->bindParam(':nTime',$nTime,PDO::PARAM_INT);
    $sendNotification->execute();
    }
    // ==================================
}

echo $followbtn;

?>