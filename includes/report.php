<?php
session_start();
include("../config/connect.php");
$r_type = htmlspecialchars(htmlentities($_POST['type']));
switch ($r_type) {
	// ================================= post =================================
case 'post':
$r_id = rand(0,999999999)+time();
$from_id = $_SESSION['id'];
$for_id = htmlspecialchars(htmlentities($_POST['fid']));
$r_time = time();
$status = "0";
$insertData = $conn->prepare("
INSERT INTO supportbox
(r_id,from_id,for_id,r_type,r_time,status) VALUES
(:r_id,:from_id,:for_id,:r_type,:r_time,:status)");
$insertData->bindParam(':r_id',$r_id,PDO::PARAM_INT);
$insertData->bindParam(':from_id',$from_id,PDO::PARAM_INT);
$insertData->bindParam(':for_id',$for_id,PDO::PARAM_INT);
$insertData->bindParam(':r_type',$r_type,PDO::PARAM_STR);
$insertData->bindParam(':r_time',$r_time,PDO::PARAM_INT);
$insertData->bindParam(':status',$status,PDO::PARAM_INT);
$insertData->execute();
if ($insertData) {
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid green;'>
	<span class='fa fa-times' onclick=\"canselPostNotify('".$for_id."')\"></span> ".lang('post_reported').".
	</p>
	";
}else{
	echo "
	<p class='postNotify' style='text-align:".lang('textAlign').";border-bottom:1px solid red;'>
	<span class='fa fa-times' onclick=\"canselPostNotify('".$for_id."')\"></span> ".lang('errorSomthingWrong')."
	</p>
	";
}
break;
	// ================================= problem =================================
case 'problem':
$r_id = rand(0,999999999)+time();
$from_id = $_SESSION['id'];
$subject = trim(filter_var(htmlentities($_POST['sub']),FILTER_SANITIZE_STRING));
$report = trim(filter_var(htmlentities($_POST['txt']),FILTER_SANITIZE_STRING));
$r_time = time();
$status = "0";
$insertData = $conn->prepare("
INSERT INTO supportbox
(r_id,from_id,r_type,subject,report,r_time,status) VALUES
(:r_id,:from_id,:r_type,:subject,:report,:r_time,:status)");
$insertData->bindParam(':r_id',$r_id,PDO::PARAM_INT);
$insertData->bindParam(':from_id',$from_id,PDO::PARAM_INT);
$insertData->bindParam(':r_type',$r_type,PDO::PARAM_STR);
$insertData->bindParam(':subject',$subject,PDO::PARAM_STR);
$insertData->bindParam(':report',$report,PDO::PARAM_STR);
$insertData->bindParam(':r_time',$r_time,PDO::PARAM_INT);
$insertData->bindParam(':status',$status,PDO::PARAM_INT);
$insertData->execute();
if ($insertData) {
	echo "done";
}else{
	echo "Error, Please try again later!";
}
break;
	// ================================= delete Report =================================
case 'deleteReport':
$r_id = htmlspecialchars(htmlentities($_POST['rid']));
$delData = $conn->prepare("DELETE FROM supportbox WHERE r_id =:r_id");
$delData->bindParam(':r_id',$r_id,PDO::PARAM_INT);
$delData->execute();
if ($delData) {
	echo "done";
}else{
	echo "error";
}
break;
}
?>