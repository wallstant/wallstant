<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include("../includes/fetch_users_info.php");
include ("../includes/time_function.php");
include ("../includes/num_k_m_count.php");
if(!isset($_SESSION['Username'])){
    header("location: ../index");
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
    <title>Saved posts | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "../includes/head_imports_main.php";?>
</head>
<body>
<!--=============================[ NavBar ]========================================-->
<?php include "../includes/navbar_main.php"; ?>
<div class="main_container" align="center">
    <div style="display: inline-flex" align="center">
        <div align="left">
        <table class="postSavedTable">
            <tr style="font-weight: bold; text-transform: uppercase; color: rgba(0, 0, 0, 0.59); font-size: 13px; background: rgb(241, 241, 241); border-bottom: 2px solid #46a0ec;">
                <td><?php echo lang('all_posts_that_you_saved'); ?></td>
                <td align="center"><span class="fa fa-cog"></span></td>
            </tr>
            <?php include "../includes/fetch_posts_saved.php"; ?>
        </table>
        <?php
        if ($countSaved < 1) {
        ?>
        <div class="saved_nothingToShow">
            <p>
            <span class="fa fa-newspaper-o" style="font-size: 62px;"></span><br>
            <?php echo lang('nothing_saved_yet'); ?>.</p>
        </div>
        <?php
        }
        ?>
        </div>
    </div>
</div>
<?php include "../includes/endJScodes.php"; ?>
</body>
</html>
