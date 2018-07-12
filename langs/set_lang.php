<?php
switch ($_SESSION['language']) {
case 'English':
	include_once $path."langs/english.php";
	break;
case 'العربية':
	include_once $path."langs/arabic.php";
	break;
default:
	$_SESSION['language'] = "English";
	include_once $path."langs/english.php";
	break;
}
?>