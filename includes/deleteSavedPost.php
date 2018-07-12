<?php
include "../config/connect.php";
$sid = filter_var(htmlspecialchars($_POST['sid']),FILTER_SANITIZE_NUMBER_INT);
$deleteS_sql = "DELETE FROM saved WHERE id = ?";
$parameter = array($sid);
$deleteS = $conn->prepare($deleteS_sql);
$deleteS->execute($parameter);
?>