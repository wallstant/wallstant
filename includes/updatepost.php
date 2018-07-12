<?php
include("../config/connect.php");
// ================= recive data from ajax data ======================================
$posts_id = htmlspecialchars($_POST['pid']);
$edit_post_var = trim(htmlspecialchars($_POST['pc']));
$edit_post_title = trim(htmlspecialchars($_POST['pt']));
$edit_post_privacy = htmlspecialchars($_POST['pp']);
$check_path_var = htmlspecialchars($_POST['cp']);
switch (filter_var(htmlspecialchars($edit_post_privacy),FILTER_SANITIZE_STRING)) {
case lang('wpr_public'):
    $p_privacy = "0";
    break;
case lang('wpr_followers'):
    $p_privacy = "1";
    break;
case lang('wpr_onlyme'):
    $p_privacy = "2";
    break;
}
// ================= PDO sql query ===================================================
$edit_post_sql = "UPDATE wpost SET post_content= :edit_post_var,p_title = :edit_post_title,p_privacy = :p_privacy WHERE post_id= :posts_id";
$edit_post = $conn->prepare($edit_post_sql);
$edit_post->bindParam(':edit_post_var',$edit_post_var,PDO::PARAM_STR);
$edit_post->bindParam(':edit_post_title',$edit_post_title,PDO::PARAM_STR);
$edit_post->bindParam(':p_privacy',$p_privacy,PDO::PARAM_INT);
$edit_post->bindParam(':posts_id',$posts_id,PDO::PARAM_INT);
$edit_post->execute();  
// ================= prepare post ====================================================
$em_img_path = $check_path_var."imgs/emoticons/";
$hashtag_path = $check_path_var."hashtag/";
include ("emoticons.php");
$post_body = str_replace($em_char,$em_img,$edit_post_var);
$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/';
$body = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $post_body);
$hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $body);
$p_body = nl2br("$body");
if (!empty($edit_post_title)) {
	$p_title = "$edit_post_title";
}else{
	$p_title = "";
}
switch ($p_privacy) {
case '0':
$postPrivacy = "<span class='fa fa-globe' data-toggle='tooltip' data-placement='top' title='".lang('wpr_public')."'></span>";
break;
case '1':
$postPrivacy = "<span class='fa fa-users' data-toggle='tooltip' data-placement='top' title='".lang('wpr_followers')."'></span>";
break;
case '2':
$postPrivacy = "<span class='fa fa-lock' data-toggle='tooltip' data-placement='top' title='".lang('wpr_onlyme')."'></span>";
break;
}

$arr = array();
$arr[0] = $p_body;
$arr[1] = $p_title;
$arr[2] = $postPrivacy;
echo json_encode($arr);
exit;
?>