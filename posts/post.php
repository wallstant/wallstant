<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$page="post";
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
    <title>Post | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "../includes/head_imports_main.php";?>
</head>
<body onload="fetchPosts_DB('home')">
<!--=============================[ NavBar ]========================================-->
<?php include "../includes/navbar_main.php"; ?>

<div class="main_container" align="center">
    <div style="display: inline-flex" align="center">
        <div align="left">
        <?php
        $post_id = filter_var(htmlentities($_GET['pid']), FILTER_SANITIZE_NUMBER_INT);
        $sid = $_SESSION['id'];
        $checkAuthOfPost_sql = "SELECT author_id FROM wpost WHERE post_id = ?";
        $checkAuthOfPost_params = array($post_id);
        $checkAuthOfPost = $conn->prepare($checkAuthOfPost_sql);
        $checkAuthOfPost->execute($checkAuthOfPost_params);
        $checkAuthOfPostCount = $checkAuthOfPost->rowCount();
        if ($checkAuthOfPostCount > 0) {
        while ($fetch_cop = $checkAuthOfPost->fetch(PDO::FETCH_ASSOC)) {
            $fetched_AuthorId = $fetch_cop['author_id'];
        }
            if ($fetched_AuthorId == $sid) {
                $fPosts_sql = "SELECT * FROM wpost WHERE post_id = ?";
                $params = array($post_id);
            }else{
                $checkFromPost_sql = "SELECT author_id FROM wpost WHERE post_id = ? AND author_id IN (SELECT uf_two FROM follow WHERE uf_one= ?)";
                $checkFromPost_params = array($post_id, $sid);
                $checkFromPost = $conn->prepare($checkFromPost_sql);
                $checkFromPost->execute($checkFromPost_params);
                $checkFromPostCount = $checkFromPost->rowCount();
                if ($checkFromPostCount < 1) {
                    $fPosts_sql = "SELECT * FROM wpost WHERE post_id = ? AND p_privacy != ? AND p_privacy != ?";
                    $params = array($post_id, "1", "2");
                }else{
                    $fPosts_sql = "SELECT * FROM wpost WHERE post_id = ? AND p_privacy != ?";
                    $params = array($post_id, "2");
                }
            }
        
        $view_posts = $conn->prepare($fPosts_sql);
        $view_posts->execute($params);
        include "../includes/fetch_posts.php";
        }else{
        ?>
        <style type="text/css">
        body{
        background: #fff;
        }
            .error_page_btn{
        background: whitesmoke;
        padding: 8px;
        border-radius: 3px;
        color: #6b6b6b;
        text-decoration: none;
        box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.05);
        transition: background 0.1s , color 0.1s;
            }
            .error_page_btn:hover, .error_page_btn:focus{
        background: #4a708e;
        color: #fff;
        text-decoration: none;
            }
            .error_div{
        padding: 15px;
        max-width: 800px;
        color: #383838;
        box-shadow: none;
        border: 1px solid rgba(217, 217, 217, 0.36);
        }
        </style>
        <div align="center" style="margin-top: 150px;margin-bottom: 150px;">
        <div class="post error_div" align="center">
        <h1 style="font-weight: bold;"><img src="../imgs/main_icons/1f915.png" style="width: 80px;height: 80px;" /> <?php echo lang('profilePageNotFound_str1'); ?></h1>
        <h3><?php echo lang('profilePageNotFound_str2'); ?></h3><br>
        <a href="javascript:history.back()" class="error_page_btn"><?php echo lang('profilePageNotFound_str3'); ?></a>
        </div></div>
        <?php
        }
        ?>
        </div>
    </div>
</div>

<?php include "../includes/endJScodes.php"; ?>
</body>
</html>
