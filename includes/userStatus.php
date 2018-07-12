<?php
// This whole code is for make user offline with ajax call when user close tab
session_start();
include "../config/connect.php";
$myid = $_SESSION['id'];
$online_status = $_POST['st'];
$setStatus = $conn->prepare("UPDATE signup SET online = :online_status WHERE id = :myid");
$setStatus->bindParam(':online_status',$online_status,PDO::PARAM_INT);
$setStatus->bindParam(':myid',$myid,PDO::PARAM_INT);
$setStatus->execute();
?>