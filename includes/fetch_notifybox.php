<?php
session_start();
include("../config/connect.php");
include ("time_function.php");
$what = htmlentities(htmlspecialchars($_POST['what']));
$path = htmlentities(htmlspecialchars($_POST['path']));
$load = htmlentities(htmlspecialchars($_POST['load']));
$myId = $_SESSION['id'];
switch ($what) {
case 'fetch':
$seen = "1";
$seenQ = $conn->prepare("UPDATE notifications SET seen=:seen WHERE for_id=:myId");
$seenQ->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenQ->bindParam(':myId',$myId,PDO::PARAM_INT);
$seenQ->execute();

$notify_sql = "SELECT * FROM notifications WHERE for_id =:myId ORDER BY time DESC LIMIT :load,10";

$notify = $conn->prepare($notify_sql);
$notify->bindParam(':myId',$myId,PDO::PARAM_INT);
$notify->bindValue(':load', (int)trim($load), PDO::PARAM_INT);
$notify->execute();
$notifyCount = $notify->rowCount();
if ($notifyCount > 0) {
while ($n_row = $notify->fetch(PDO::FETCH_ASSOC)) {
	$notify_id = $n_row['n_id'];
	$notify_from_id = $n_row['from_id'];
	$notify_for_id = $n_row['for_id'];
	$notifyType_id= $n_row['notifyType_id'];
	$notifyType = $n_row['notifyType'];
	$notify_seen = $n_row['seen'];
	$notify_time = time_ago($n_row['time']);

$notify_from = $conn->prepare("SELECT Fullname,Username,Userphoto FROM signup WHERE id=:notify_from_id");
$notify_from->bindParam(':notify_from_id',$notify_from_id,PDO::PARAM_INT);
$notify_from->execute();
while ($from_id_row = $notify_from->fetch(PDO::FETCH_ASSOC)) {
	$fullname = $from_id_row['Fullname'];
	$userphoto = $from_id_row['Userphoto'];
	$username = $from_id_row['Username'];
}
switch ($notifyType) {
case 'like':
$postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
$postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$postBody->execute();
while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
	$postContent = $row['post_content'];
}
if (strlen($postContent) > 70) {
	$getPCon = " : ".substr($postContent, 0,70)." ...";
}elseif (empty($postContent)) {
	$getPCon = "";
}else{
	$getPCon = " : ".$postContent;
}
echo "
<div id='sqresultItem'>
<a href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('likeNotify_str')." <span style='color: #999;'>$getPCon</span>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f49f.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";
break;
case 'comment':
echo "
<div id='sqresultItem'>
<a href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('commmentNotify_str').".
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f5e8.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";
break;
case 'share':
$postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
$postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$postBody->execute();
while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
	$postContent = $row['post_content'];
}
if (strlen($postContent) > 70) {
	$getPCon = " : ".substr($postContent, 0,70)." ...";
}elseif (empty($postContent)) {
	$getPCon = "";
}else{
	$getPCon = " : ".$postContent;
}
echo "
<div id='sqresultItem'>
<a href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('shareNotify_str')." <span style='color: #999;'>$getPCon</span>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f504.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";
break;
case 'star':
$getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
$getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$getUsername->execute();
while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
	$pUsername = $row['Username'];
}
echo "
<div id='sqresultItem'>
<a href='".$path."u/".$pUsername."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'>".lang('starNotify_str')." <b>$fullname</b>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/2b50.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";
break;
case 'follow':
$getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
$getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$getUsername->execute();
while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
	$pUsername = $row['Username'];
}
echo "
<div id='sqresultItem'>
<a href='".$path."u/".$pUsername."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('followNotify_str')."
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f465.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";
break;
}
}
}else{
	echo "0";
}
break;
// =============================================================
case 'check':
$seen ="0";
$notifyCheck = $conn->prepare("SELECT seen FROM notifications WHERE for_id=:myId AND seen =:seen");
$notifyCheck->bindParam(':seen',$seen,PDO::PARAM_INT);
$notifyCheck->bindParam(':myId',$myId,PDO::PARAM_INT);
$notifyCheck->execute();
$notifyCheckCount = $notifyCheck->rowCount();
echo $notifyCheckCount;

break;
}
?>