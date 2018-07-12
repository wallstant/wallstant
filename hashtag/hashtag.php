<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include ("../includes/time_function.php");
include ("../includes/num_k_m_count.php");
if(!isset($_SESSION['Username'])){
    header("location: ../home");
}
if (is_dir("imgs/")) {
        $check_path = "";
    }elseif (is_dir("../imgs/")) {
        $check_path = "../";
    }elseif (is_dir("../../imgs/")) {
        $check_path = "../../";
    }
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><?php echo "Hashtag - ".$_GET['tag'];if($_GET['tag'] == 'search'){echo "Hashtag";} ?> | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="hashtag,social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
    .selected_ht{
    background: rgba(0, 144, 255, 0.8);
    padding: 3px 5px;
    border-radius: 3px;
    box-shadow: inset 1px 1px 5px rgba(0, 0, 0, 0.06);
    color: #ffffff;
    cursor: pointer;
    text-decoration: none;
    }
    .selected_ht:hover,.selected_ht:focus{
    color: #ffffff;
    text-decoration: none;
    }
    </style>
    <?php include "../includes/head_imports_main.php";?>
</head>
<body onload="hide_notify()">
<!--=============================[ NavBar ]========================================-->
<?php include "../includes/navbar_main.php"; ?>
<!--=============================[ Div_Container ]========================================-->
<div class="main_container" align="center">
    <div style="display: inline-flex" align="center">
        <div align="left">
<?php
$s_id = $_SESSION['id'];
if(isset($_GET['tag'])){
$q = "#".htmlentities($_GET['tag'], ENT_QUOTES);
$search_sql = "SELECT * FROM wpost WHERE post_content LIKE ? AND p_privacy != ? AND p_privacy != ? ORDER BY post_time DESC LIMIT 8";
$params = array("%$q%", "1", "2");
$view_posts = $conn->prepare($search_sql);
$view_posts->execute($params);
$search_num = $view_posts->rowCount();
if ($search_num > 0) {
$isHashTagPage="yep";
include "../includes/fetch_posts.php";
}else{
?>
<div class="post">
<table class='user_follow_box'>
<tr>
<td><img src='../imgs/main_icons/2139.png' style='border-radius: 100%;width:52px;height:52px;' /></td>
<td style='width: 100%;'>
<p><?php echo lang('nothingToShow'); ?><br><span style='font-size: small;color:gray;'><?php echo lang('hashtag_not_available'); ?></span></p>
</td>
</tr>
<tr style="margin: 10px">
<td></td>
    <td>
<hr>
<?php
$s_country = $_SESSION['country'];
$s_country = str_replace(' ', '_', $s_country);
echo "
    <a href='../hashtag/".lang('news')."' class='selected_ht' title='#".lang('news')."'>#".lang('news')."</a>&nbsp;
    <a href='../hashtag/".lang('tv')."' class='selected_ht' title='#".lang('tv')."'>#".lang('tv')."</a> &nbsp;
    <a href='../hashtag/".lang('store')."' class='selected_ht' title='#".lang('store')."'>#".lang('store')."</a> &nbsp;
    <a href='../hashtag/$s_country' class='selected_ht' title='$s_country'>#$s_country</a> &nbsp;
    <a href='../hashtag/".lang('new')."' class='selected_ht' title='#".lang('new')."'>#".lang('new')."</a> &nbsp;
    <a href='../hashtag/".lang('job')."' class='selected_ht' title='#".lang('job')."'>#".lang('job')."</a> &nbsp;
    "; ?>
    </td>
</tr>
</table>
</div>
<?php 
}
}
?>
</div></div>
</div>

</div>
<!--===============================[ End ]==========================================-->
<?php include "../includes/endJScodes.php"; ?>
<?php include("../includes/footer.php");?>
</body>
</html>