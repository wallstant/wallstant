<?php
include("../config/connect.php");
session_start();
$sid = $_SESSION['id'];
$c_id = htmlentities($_POST['cid'], ENT_QUOTES);
$p_id = htmlentities($_POST['pid'], ENT_QUOTES);
$check = $conn->prepare("SELECT c_author_id FROM comments WHERE c_id =:c_id");
$check->bindParam(':c_id',$c_id,PDO::PARAM_INT);
$check->execute();
while ($chR = $check->fetch(PDO::FETCH_ASSOC)) {
	$chR_aid = $chR['c_author_id'];
}
if ($chR_aid == $sid) {
	$delete_comm_sql = "DELETE FROM comments WHERE c_id= :c_id";
	$delete_comm = $conn->prepare($delete_comm_sql);
	$delete_comm->bindParam(':c_id',$c_id,PDO::PARAM_INT);
	$delete_comm->execute();
	echo "yes";
}else{
	echo "no";
}

?>