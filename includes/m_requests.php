<?php
// start session
session_start();
// include irequired files
include("../config/connect.php");
include ("time_function.php");
// main varaibles
$myid = $_SESSION['id'];
$req = filter_var(htmlentities($_POST['req']),FILTER_SANITIZE_STRING);
$path = filter_var(htmlentities($_POST['path']),FILTER_SANITIZE_STRING);
// create [switch] to do the requested code by [$req] variable above
switch ($req) {
// =================== if ajax requested [getUsers] do this code ===================
case 'getUsers':
// select users that I follow
$users = $conn->prepare("SELECT uf_two FROM follow WHERE uf_one=:myid");
$users->bindParam(':myid',$myid,PDO::PARAM_INT);
$users->execute();
$usersCount = $users->rowCount();
if ($usersCount > 0) {
while ($uf = $users->fetch(PDO::FETCH_ASSOC)) {
$uf_two = $uf['uf_two'];
$usersInfo = $conn->prepare("SELECT id,online,Fullname,Userphoto,Username,verify FROM signup WHERE id=:uf_two");
$usersInfo->bindParam(':uf_two',$uf_two,PDO::PARAM_INT);
$usersInfo->execute();

while ($uRows = $usersInfo->fetch(PDO::FETCH_ASSOC)) {
// if user online make online circle green, If not make it silver or gray
if ($uRows['online'] == "1") {
	$m_userActive = "#4CAF50";
}else{
	$m_userActive = "#ccc";
}
// get last msg as hint
$uid = $uRows['id'];
$getHint = $conn->prepare("SELECT message,m_from FROM messages WHERE (m_from = :myid AND m_to = :uid) OR (m_from = :uid AND m_to = :myid) ORDER BY m_time DESC LIMIT 1");
$getHint->bindParam(':myid',$myid,PDO::PARAM_INT);
$getHint->bindParam(':uid',$uid,PDO::PARAM_INT);
$getHint->execute();
$getHintCount = $getHint->rowCount();
// check if I have messages with this user [uid] then get last message as hint, If not echo his Username instead
if ($getHintCount > 0) {
	while ($getHint_row = $getHint->fetch(PDO::FETCH_ASSOC)) {
		$lastMsg = $getHint_row['message'];
		$lastMsg_from = $getHint_row['m_from'];
		if (strlen($lastMsg) > 20) {
			$lastMsg = substr($lastMsg, 0,20)."...";
		}
	if ($lastMsg_from == $myid) {
		$lastMsg = "<span style='color:#2196F3 !important;'>".lang('you')." :</span> ".$lastMsg;
	}
	}
}else{
	$lastMsg = "@".$uRows['Username'];
}
// if user verified echo verify badge , If not do nothing
if ($uRows['verify'] == "1"){
    $verifypage_var = $verifyUser;
}else{
    $verifypage_var = "";
}
// count unseen messages
$uid = $uRows['id'];
$seen = "0";
$seenCount = $conn->prepare("SELECT message FROM messages WHERE (m_from=:uid AND m_to=:myid) AND m_seen = :seen");
$seenCount->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenCount->bindParam(':uid',$uid,PDO::PARAM_INT);
$seenCount->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenCount->execute();
$seenCountNum = $seenCount->rowCount();
if ($seenCountNum > 0) {
	$mCountUnseen = "<span class='mNew_notifi'>$seenCountNum</span>";
}else{
	$mCountUnseen = "";
}
// send result
echo "
<table class=\"m_contacts_table\">
	<tr class=\"mC_userLink\" data-muid=\"".$uRows['id']."\">
		<td style=\"width: 44px;position: relative;\">
			<div class=\"m_contacts_user\">
				<div class=\"m_userActive\" style=\"background:".$m_userActive.";".lang('float2').":8px;\"></div>
				<img src=\"".$path."imgs/user_imgs/".$uRows['Userphoto']."\">
			</div>
		</td>
		<td>
			<p>".$uRows['Fullname']."$verifypage_var<span id=\"msgsCount\" style=\"float:".lang('float2')."\">".$mCountUnseen."</span><br><span style=\"word-break: break-word;font-size: 12px;color: #d2d2d2;\">".$lastMsg."</span></p>
		</td>
	</tr>
</table>
";	
}
}
}else{
	echo "";
}
break;
// =================== if ajax requested [getUsers2] do this code ===================
case 'getUsers2':
// select users that they sent to me messages and/or they follow me
$users2 = $conn->prepare("SELECT count(id),m_from FROM messages WHERE m_to=:myid GROUP BY m_from");
$users2->bindParam(':myid',$myid,PDO::PARAM_INT);
$users2->execute();
$users2Count = $users2->rowCount();
if ($users2Count > 0) {
while ($uf2 = $users2->fetch(PDO::FETCH_ASSOC)) {
// get id of [users] and [users2] queries
$m_from = $uf2['m_from'];
$usersInfo = $conn->prepare("SELECT id,online,Fullname,Userphoto,Username,verify FROM signup WHERE id=:m_from");
$usersInfo->bindParam(':m_from',$m_from,PDO::PARAM_INT);
$usersInfo->execute();
while ($uRows = $usersInfo->fetch(PDO::FETCH_ASSOC)) {
// if user online make online circle green, If not make it silver or gray
if ($uRows['online'] == "1") {
	$m_userActive = "#4CAF50";
}else{
	$m_userActive = "#ccc";
}
// get last msg as hint
$uid = $uRows['id'];
$getHint = $conn->prepare("SELECT message,m_from FROM messages WHERE (m_from = :myid AND m_to = :uid) OR (m_from = :uid AND m_to = :myid) ORDER BY m_time DESC LIMIT 1");
$getHint->bindParam(':myid',$myid,PDO::PARAM_INT);
$getHint->bindParam(':uid',$uid,PDO::PARAM_INT);
$getHint->execute();
$getHintCount = $getHint->rowCount();
// check if I have messages with this user [uid] then get last message as hint, If not echo his Username instead
if ($getHintCount > 0) {
	while ($getHint_row = $getHint->fetch(PDO::FETCH_ASSOC)) {
		$lastMsg = $getHint_row['message'];
		$lastMsg_from = $getHint_row['m_from'];
		if (strlen($lastMsg) > 20) {
			$lastMsg = substr($lastMsg, 0,20)."...";
		}
	if ($lastMsg_from == $myid) {
		$lastMsg = "<span style='color:#2196F3 !important;'>".lang('you')." :</span> ".$lastMsg;
	}
	}
}else{
	$lastMsg = "@".$uRows['Username'];
}
// if user verified echo verify badge , If not do nothing
if ($uRows['verify'] == "1"){
    $verifypage_var = $verifyUser;
}else{
    $verifypage_var = "";
}
// count unseen messages
$uid = $uRows['id'];
$seen = "0";
$seenCount = $conn->prepare("SELECT message FROM messages WHERE (m_from=:uid AND m_to=:myid) AND m_seen = :seen");
$seenCount->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenCount->bindParam(':uid',$uid,PDO::PARAM_INT);
$seenCount->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenCount->execute();
$seenCountNum = $seenCount->rowCount();
if ($seenCountNum > 0) {
	$mCountUnseen = "<span class='mNew_notifi'>$seenCountNum</span>";
}else{
	$mCountUnseen = "";
}
// check if user who requested a message is one of my friends or not
$uReqCheck = $conn->prepare("SELECT uf_two FROM follow WHERE uf_one=:myid AND uf_two = :m_from");
$uReqCheck->bindParam(':myid',$myid,PDO::PARAM_INT);
$uReqCheck->bindParam(':m_from',$m_from,PDO::PARAM_INT);
$uReqCheck->execute();
$uReqCheckCount = $uReqCheck->rowCount();
if ($uReqCheckCount < 1) {
// send result
echo "
<table class=\"m_contacts_table\">
	<tr class=\"mC_userLink\" data-muid=\"".$uRows['id']."\">
		<td style=\"width: 44px;position: relative;\">
			<div class=\"m_contacts_user\">
				<div class=\"m_userActive\" style=\"background:".$m_userActive.";".lang('float2').":8px;\"></div>
				<img src=\"".$path."imgs/user_imgs/".$uRows['Userphoto']."\">
			</div>
		</td>
		<td>
			<p>".$uRows['Fullname']."$verifypage_var<span id=\"msgsCount\" style=\"float:".lang('float2')."\">".$mCountUnseen."</span><br><span style=\"word-break: break-word;font-size: 12px;color: #d2d2d2;\">".$lastMsg."</span></p>
		</td>
	</tr>
</table>
";	
}
}
}
}else{
	echo "";
}
break;
// =================== if ajax requested [searchUser] do this code ===================
case 'searchUser':
// search in users that I follow
$mSearch = filter_var(htmlentities($_POST['mSearch']),FILTER_SANITIZE_STRING);
$uSearch = $conn->prepare("SELECT id,online,Fullname,Userphoto,Username,verify FROM signup WHERE id IN (SELECT uf_two FROM follow WHERE uf_one= ?) AND (Fullname LIKE ? OR Username LIKE ?)");
$params = array("$myid","$mSearch%","$mSearch%");
$uSearch->execute($params);
$uSearchCount = $uSearch->rowCount();
if ($uSearchCount > 0) {
while ($uSearch_row = $uSearch->fetch(PDO::FETCH_ASSOC)) {
// if user online make online circle green, If not make it silver or gray
if ($uSearch_row['online'] == "1") {
	$m_userActive = "#4CAF50";
}else{
	$m_userActive = "#ccc";
}
// if user verified echo verify badge , If not do nothing
if ($uSearch_row['verify'] == "1"){
    $verifypage_var = $verifyUser;
}else{
    $verifypage_var = "";
}
// send result
echo "
<table class=\"m_contacts_table\">
	<tr class=\"mC_userLink\" data-muid=\"".$uSearch_row['id']."\">
		<td style=\"width: 44px;position: relative;\">
			<div class=\"m_contacts_user\">
				<div class=\"m_userActive\" style=\"background:".$m_userActive.";".lang('float2').":8px;\"></div>
				<img src=\"".$path."imgs/user_imgs/".$uSearch_row['Userphoto']."\">
			</div>
		</td>
		<td>
			<p>".$uSearch_row['Fullname']."$verifypage_var<span id=\"msgsCount\" style=\"float:".lang('float2')."\"></span><br><span style=\"font-size: 12px;color: #d2d2d2;\">@".$uSearch_row['Username']."</span></p>
		</td>
	</tr>
</table>
";
}
}else{
	echo "<p style='text-align: center; padding: 15px; color: #c7c7c7; font-weight: bold;'><span class='fa fa-search' style='font-size: 32px; margin-bottom: 15px; color: #eaeaea;'></span><br>".lang('nothingToShow')."</p>";
}
break;
// =================== if ajax requested [userProfile] do this code ===================
case 'userProfile':
// get [user info] that I selected from contacts
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
$getInfo = $conn->prepare("SELECT Username,Fullname,Userphoto,online,bio,verify FROM signup WHERE id=:uid");
$getInfo->bindParam(':uid',$uid,PDO::PARAM_INT);
$getInfo->execute();
while ($getInfo_row = $getInfo->fetch(PDO::FETCH_ASSOC)) {
// if user online make online circle green and make [online status = Active], If not make it silver or gray and make [online status = Not active]
if ($getInfo_row['online'] == "1") {
	$onlineColor = "#4CAF50";
	$onlineStatus = lang('activeNow');
}else{
	$onlineColor = "#ccc";
	$onlineStatus = lang('notActiveNow');
}
// if user verified echo verify badge , If not do nothing
if ($getInfo_row['verify'] == "1"){
    $verifypage_var = $verifyUser;
}else{
    $verifypage_var = "";
}
// send [userInfo] result in variable to push it in [json array]
$userInfo = "
<div style='position: relative;'>
<div class='mCol3_userInfo_avatar'>
	<img src='".$path."imgs/user_imgs/".$getInfo_row['Userphoto']."' alt='".$getInfo_row['Username']."'>
</div>
<div class='mCol3_userActive' style='background:".$onlineColor.";".lang('float2').":55%;'></div>
</div>
<h4 style='text-align: center;'><a href='".$path."u/".$getInfo_row['Username']."' class='mCol3_userProfileLink'>".$getInfo_row['Fullname']."</a> ".$verifypage_var."</h4>
<p style='text-align:center;margin: 0px;color: gray'>@".$getInfo_row['Username']." | <span style='color: ".$onlineColor.";'>".$onlineStatus."</span></p>
";
// send [userInfo_bio] result in variable to push it in [json array]
$userInfo_bio = "
<p style='color: #808080;'>
<span style='color: #808080; font-weight: bold;'>".lang('bio')." :</span><br>
".$getInfo_row['bio']."
</p>
";
// send [userFullname] result in variable to push it in [json array]
$userFullname = $getInfo_row['Fullname'];
}
// [json array] to send data as array
$arr = array();
$arr[0] = $userInfo;
$arr[1] = $userInfo_bio;
$arr[2] = $userFullname;
echo json_encode($arr);
break;
// =================== if ajax requested [fetchMsgs] do this code ===================
case 'fetchMsgs':
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
// m_seen set to seen
$seen = "1";
$seenUpdate = $conn->prepare("UPDATE messages SET m_seen = :seen WHERE m_from=:uid AND m_to=:myid");
$seenUpdate->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenUpdate->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenUpdate->bindParam(':uid',$uid,PDO::PARAM_INT);
$seenUpdate->execute();
// select messages and fetch
$getMsgs = $conn->prepare("SELECT * FROM messages WHERE (m_from = :myid AND m_to = :uid) OR (m_from = :uid AND m_to = :myid)");
$getMsgs->bindParam(':myid',$myid,PDO::PARAM_INT);
$getMsgs->bindParam(':uid',$uid,PDO::PARAM_INT);
$getMsgs->execute();
$getMsgsCout = $getMsgs->rowCount();
// if selected messages more than zero [0] do this, If not >> do [else] code
if ($getMsgsCout > 0) {
while ($msgs_row = $getMsgs->fetch(PDO::FETCH_ASSOC)) {
	$toUserQuery = $conn->prepare("SELECT Userphoto FROM signup WHERE id = :uid");
	$toUserQuery->bindParam(':uid',$uid,PDO::PARAM_INT);
	$toUserQuery->execute();
	// get msg user into
	while ($toUser = $toUserQuery->fetch(PDO::FETCH_ASSOC)) {
		$userPhoto = $toUser['Userphoto'];
	}
// set message style for me and the other user that I chatting with him
if ($msgs_row['m_from'] == $myid) {
	$m_msgU = "m_msgU2";
	$userDir = "rtl";
	$msgUserPhoto = "";
}else{
	$m_msgU = "m_msgU1";
	$userDir = "ltr";
	$msgUserPhoto = "<td style='position: relative; width: 30px;'><div class='m_msgUserImg'><img src='".$path."imgs/user_imgs/".$userPhoto."'></div></td>";
}
// set time variable
$mTime = time_ago($msgs_row['m_time']);
//setting up message 
$em_img_path = $path."imgs/emoticons/";
include ("emoticons.php");
$message_body = str_replace($em_char,$em_img,$msgs_row['message']);
$hashtag_path = $path."hashtag/";
$hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$message_body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $message_body);
$message_body = nl2br($message_body);
// send result
echo "
<table class='m_msgTable' data-count='".$getMsgsCout."' style='direction:".$userDir.";'>
	<tr>
		".$msgUserPhoto."
		<td>
			<div class='".$m_msgU."' style='direction:".lang('dir').";' title='".date("d M, Y",$msgs_row['m_time'])."'>
			<p style='margin:0;display: inline;'>".$message_body."</p><sub style='font-size: 11px; margin:0px 8px; font-weight: bold;display: inline-block;'>".$mTime."</sub>
			</div>
		</td>
	</tr>
</table>
";
}
}else{
// [else] code .. this mean that there are not messages to show
echo  "
<p class='selectToChat'>
	".lang('emptyChat')."
</p>
";
}
break;
// =================== if ajax requested [sendMsg] do this code ===================
case 'sendMsg':
// set required variables to send it to database [send message]
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
$msg = trim(filter_var(htmlentities($_POST['msg']),FILTER_SANITIZE_STRING));
$mid = rand(0,999999999)+time();
$mTime = time();
$mSeen="0";
// send message to database
$insertM = $conn->prepare("INSERT INTO messages (m_id,message,m_from,m_to,m_time,m_seen) VALUES (:mid,:msg,:myid,:uid,:mTime,:mSeen)");
$insertM->bindParam(':mid',$mid,PDO::PARAM_INT);
$insertM->bindParam(':msg',$msg,PDO::PARAM_STR);
$insertM->bindParam(':myid',$myid,PDO::PARAM_INT);
$insertM->bindParam(':uid',$uid,PDO::PARAM_INT);
$insertM->bindParam(':mTime',$mTime,PDO::PARAM_INT);
$insertM->bindParam(':mSeen',$mSeen,PDO::PARAM_INT);
$insertM->execute();
// if message sent successfully do nothing, If not give me an error
if ($insertM) {
}else{
	echo "error";
}
break;
// =================== if ajax requested [checkSeen] do this code ===================
case 'checkSeen':
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
// set messages of user [uid] above as [seen]
$seenCheck = $conn->prepare("SELECT m_seen,m_to FROM messages WHERE (m_from = :myid AND m_to = :uid) OR (m_from = :uid AND m_to = :myid) ORDER BY m_time DESC LIMIT 1");
$seenCheck->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenCheck->bindParam(':uid',$uid,PDO::PARAM_INT);
$seenCheck->execute();
while ($chSeen = $seenCheck->fetch(PDO::FETCH_ASSOC)) {
	$seenStatus = $chSeen['m_seen'];
	$getM_from = $chSeen['m_to'];
	if ($getM_from == $myid) {
		echo "0";
	}else{
		echo $seenStatus;
	}
}
break;
// =================== if ajax requested [checkUnseenMsgs] do this code ===================
case 'checkUnseenMsgs':
$seen = "0";
$seenCount = $conn->prepare("SELECT message FROM messages WHERE m_to=:myid AND m_seen = :seen");
$seenCount->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenCount->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenCount->execute();
$seenCountNum = $seenCount->rowCount();
if ($seenCountNum > 0) {
	echo "<span class='redAlert_notify_msgs'>$seenCountNum</span>";
}else{
	echo "";
}	
break;
// =================== if ajax requested [checkTyping] do this code ===================
case 'checkTyping':
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
$typing = $conn->prepare("SELECT t_from FROM typing_m WHERE t_from=:uid AND t_to = :myid");
$typing->bindParam(':uid',$uid,PDO::PARAM_INT);
$typing->bindParam(':myid',$myid,PDO::PARAM_INT);
$typing->execute();
$typingCount = $typing->rowCount();
echo $typingCount;
break;
// =================== if ajax requested [mTyping] do this code ===================
case 'mTyping':
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
$typingExist = $conn->prepare("SELECT t_from FROM typing_m WHERE t_from=:myid AND t_to = :uid");
$typingExist->bindParam(':uid',$uid,PDO::PARAM_INT);
$typingExist->bindParam(':myid',$myid,PDO::PARAM_INT);
$typingExist->execute();
$typingExistCount = $typingExist->rowCount();
if ($typingExistCount < 1) {
$setTyping = $conn->prepare("INSERT INTO typing_m (t_from,t_to) VALUES (:myid,:uid)");
$setTyping->bindParam(':uid',$uid,PDO::PARAM_INT);
$setTyping->bindParam(':myid',$myid,PDO::PARAM_INT);
$setTyping->execute();
}else{

}
break;
// =================== if ajax requested [mUnTyping] do this code ===================
case 'mUnTyping':
$uid = filter_var(htmlentities($_POST['uid']),FILTER_SANITIZE_NUMBER_INT);
$unTyping = $conn->prepare("DELETE FROM typing_m WHERE t_from=:myid AND t_to = :uid");
$unTyping->bindParam(':uid',$uid,PDO::PARAM_INT);
$unTyping->bindParam(':myid',$myid,PDO::PARAM_INT);
$unTyping->execute();
break;
}


?>
