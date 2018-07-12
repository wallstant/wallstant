<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include ("../includes/num_k_m_count.php");
include ("../includes/time_function.php");

// if user not logged in go index page or login
if(!isset($_SESSION['Username'])){
    header("location: ../index");
}

// check if user is an admin or naot to access dashboard
if ($_SESSION['admin'] != '1') {
    if ($_SESSION['admin'] != '2') {
        header("location: ../index");
    }
}

// set check path var
if (is_dir("imgs/")) {
    $dircheckPath = "";
}elseif (is_dir("../imgs/")) {
    $dircheckPath = "../";
}elseif (is_dir("../../imgs/")) {
    $dircheckPath = "../../";
}

//get request 'urlP'
$urlP = filter_var(htmlspecialchars($_GET['adb']),FILTER_SANITIZE_STRING);
// ============= [ General Data ] ==============
$cusers_q_sql = "SELECT id FROM signup";
$cusers_q = $conn->prepare($cusers_q_sql);
$cusers_q->execute();
$cusers_q_num_rows = $cusers_q->rowCount();

$cposts_q_sql = "SELECT post_id FROM wpost";
$cposts_q = $conn->prepare($cposts_q_sql);
$cposts_q->execute();
$cposts_q_num_rows = $cposts_q->rowCount();

$ccomments_q_sql = "SELECT c_id FROM comments";
$ccomments_q = $conn->prepare($ccomments_q_sql);
$ccomments_q->execute();
$ccomments_q_num_rows = $ccomments_q->rowCount();

$verify_q_sql = "SELECT verify FROM signup WHERE verify='1'";
$verify_q = $conn->prepare($verify_q_sql);
$verify_q->execute();
$verify_q_num_rows = $verify_q->rowCount();

$admins_q_sql = "SELECT admin FROM signup WHERE admin='1' OR admin='2'";
$admins_q = $conn->prepare($admins_q_sql);
$admins_q->execute();
$admins_q_num_rows = $admins_q->rowCount();

$likes_q_sql = "SELECT id FROM likes";
$likes_q = $conn->prepare($likes_q_sql);
$likes_q->execute();
$likes_q_num_rows = $likes_q->rowCount();

$aSetup_q_sql = "SELECT aSetup FROM signup WHERE aSetup='100'";
$aSetup = $conn->prepare($aSetup_q_sql);
$aSetup->execute();
$aSetup_num_rows = $aSetup->rowCount();

$genderM_q_sql = "SELECT gender FROM signup WHERE gender='Male'";
$genderM = $conn->prepare($genderM_q_sql);
$genderM->execute();
$genderM_num_rows = $genderM->rowCount();

$genderF_sql = "SELECT gender FROM signup WHERE gender='Female'";
$genderF = $conn->prepare($genderF_sql);
$genderF->execute();
$genderF_num_rows = $genderF->rowCount();

$stars_q_sql = "SELECT id FROM r_star";
$stars_q = $conn->prepare($stars_q_sql);
$stars_q->execute();
$stars_q_num_rows = $stars_q->rowCount();

$notes_sql = "SELECT id FROM mynotepad";
$notes_q = $conn->prepare($notes_sql);
$notes_q->execute();
$notes_q_num_rows = $notes_q->rowCount();

$msgs_sql = "SELECT id FROM messages";
$msgs_sql = $conn->prepare($msgs_sql);
$msgs_sql->execute();
$msgs_sql_count = $msgs_sql->rowCount();

$saved_sql = "SELECT id FROM saved";
$saved_q = $conn->prepare($saved_sql);
$saved_q->execute();
$saved_q_num_rows = $saved_q->rowCount();

$notifications_sql = "SELECT id FROM notifications";
$notifications_q = $conn->prepare($notifications_sql);
$notifications_q->execute();
$notifications_q_num_rows = $notifications_q->rowCount();

$users = thousandsCurrencyFormat($cusers_q_num_rows);
$posts = thousandsCurrencyFormat($cposts_q_num_rows);
$comments = thousandsCurrencyFormat($ccomments_q_num_rows);
$verified = thousandsCurrencyFormat($verify_q_num_rows);
$admins = thousandsCurrencyFormat($admins_q_num_rows);
$likes = thousandsCurrencyFormat($likes_q_num_rows);
$aSetup = thousandsCurrencyFormat($aSetup_num_rows);
$genderM = thousandsCurrencyFormat($genderM_num_rows);
$genderF = thousandsCurrencyFormat($genderF_num_rows);
$stars = thousandsCurrencyFormat($stars_q_num_rows);
$notes = thousandsCurrencyFormat($notes_q_num_rows);
$msgs = thousandsCurrencyFormat($msgs_sql_count);
$saved = thousandsCurrencyFormat($saved_q_num_rows);
$notifications = thousandsCurrencyFormat($notifications_q_num_rows);
// ============= [ Verify badge ] ==============
if (isset($_POST['verifyBadgeBtn'])) {
    $userVerifyBadge = htmlspecialchars(htmlentities($_POST['verifyBadge']));
    $verifyUserE_sql = "SELECT Username FROM signup WHERE Username=:userVerifyBadge";
    $verifyUserE = $conn->prepare($verifyUserE_sql);
    $verifyUserE->bindParam(':userVerifyBadge',$userVerifyBadge,PDO::PARAM_STR);
    $verifyUserE->execute();
    $verifyUserE_count = $verifyUserE->rowCount();
    if ($verifyUserE_count > 0) {
    switch (filter_var(htmlentities($_POST['verifyOptions']),FILTER_SANITIZE_STRING)) {
        case lang('verify_user'):
        $verifyValue = "1";
        $insertVerify_sql = "UPDATE signup SET verify=:verifyValue WHERE Username =:userVerifyBadge";
        $insertVerify = $conn->prepare($insertVerify_sql);
        $insertVerify->bindParam(':verifyValue',$verifyValue,PDO::PARAM_INT);
        $insertVerify->bindParam(':userVerifyBadge',$userVerifyBadge,PDO::PARAM_STR);
        $insertVerify->execute();
        if ($insertVerify) {
            $verifyBadgeResult = "<p style='color:#4CAF50;'><a href='".$dircheckPath."u/".$userVerifyBadge."'> $userVerifyBadge</a> ".lang('verified_successfully')."</p>";
        }else{
            $verifyBadgeResult = "<p style='color:#F44336;'> ".lang('errorSomthingWrong')."</p>";
        }
        break;
        case lang('remove_verifyBadge'):
        $verifyValue = "0";
        $insertVerify_sql = "UPDATE signup SET verify=:verifyValue WHERE Username =:userVerifyBadge";
        $insertVerify = $conn->prepare($insertVerify_sql);
        $insertVerify->bindParam(':verifyValue',$verifyValue,PDO::PARAM_INT);
        $insertVerify->bindParam(':userVerifyBadge',$userVerifyBadge,PDO::PARAM_STR);
        $insertVerify->execute();
        if ($insertVerify) {
            $verifyBadgeResult = "<p style='color:#4CAF50;'>".lang('verify_badge_removed_succ_from')." <a href='".$dircheckPath."u/".$userVerifyBadge."'> $userVerifyBadge</a></p>";
        }else{
            $verifyBadgeResult = "<p style='color:#F44336;'> ".lang('errorSomthingWrong')."</p>";
        }
        break;
    }
    }else{
        $verifyBadgeResult = "<p style='color:#F44336;'>".lang('user_doesnt_exist')."</p>";
    }
}
// ============= [ Fetch users ] ==============

// ============= [ ============ ] ==============
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><? echo lang('dashboard'); ?> | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "../includes/head_imports_main.php";?>
</head>
<body>
<?php include "../includes/navbar_main.php"; ?>
<div align="center" style="margin-top: 54px; padding: 3%;">
<div class="dashboard_box" style="text-align: <? echo lang('textAlign'); ?>">
    <div class="dashboard_boxD1">
        <ul>
            <li>
                <a href="index?adb=General">
                    <p class="<? if($urlP=='General' or $urlP==''){echo'dboard_lActive';} ?>"><? echo lang('general'); ?></p>
                </a>
            </li>
            <li>
                <a href="index?adb=verify">
                    <p class="<? if($urlP=='verify'){echo'dboard_lActive';} ?>"><? echo lang('verify_badge'); ?></p>
                </a>
            </li>
            <li>
                <a href="index?adb=Users">
                    <p class="<? if($urlP=='Users'){echo'dboard_lActive';} ?>"><? echo lang('users'); ?></p>
                </a>
            </li>
            <li>
                <a href="index?adb=Support_box">
                    <p class="<? if($urlP=='Support_box'){echo'dboard_lActive';} ?>"><? echo lang('supportBox'); ?></p>
                </a>
            </li>
        </ul>
    </div>
    <div class="dashboard_boxD2">
    <!--///////////////General/////////////////////-->
    <?php switch ($urlP) { 
        case 'General': ?>
    <p class="dashboard_path"><a href="?adb=General"><? echo lang('dashboard'); ?></a> / <? echo lang('general'); ?></p>
    <div id="General">
        <div id="1row" style="display: flex;">
            <div class="details">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-users"></span>
               <p><h3><?php echo $users; ?></h3> <? echo lang('users'); ?></p>
            </div>
            <div class="details">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-male"></span>
               <p><h3><?php echo $genderM; ?></h3> <? echo lang('males'); ?></p>
            </div>
            <div class="details">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-female"></span>
               <p><h3><?php echo $genderF; ?></h3> <? echo lang('females'); ?></p>
            </div>
        </div>
        <div id="row2" style="display: flex;">
            <div class="details" style="background: #673AB7;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-envelope"></span>
               <p><h3><? echo $msgs; ?></h3> <? echo lang('messages'); ?></p>
            </div>
            <div class="details" style="background: #9C27B0;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-bell"></span>
               <p><h3><?php echo $notifications; ?></h3> <? echo lang('notifications'); ?></p>
            </div>
            <div class="details" style="background: #FFC107;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-check"></span>
               <p><h3><?php echo $aSetup; ?></h3> <? echo lang('completed_profiles'); ?></p>
            </div>
        </div>
        <div id="3row" style="display: flex;">
            <div class="details" style="background: #e91e63;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-pencil"></span>
               <p><h3><?php echo $posts; ?></h3> <? echo lang('posts_str'); ?></p>
            </div>
            <div class="details" style="background: #ff9800;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-commenting-o"></span>
               <p><h3><?php echo $comments; ?></h3> <? echo lang('comments'); ?></p>
            </div>
            <div class="details" style="background: #FF5722;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-heart-o"></span>
               <p><h3><?php echo $likes; ?></h3> <? echo lang('likes'); ?></p>
            </div>
            <div class="details" style="background: #ff9800;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-star-o"></span>
               <p><h3><?php echo $stars; ?></h3> <? echo lang('stars'); ?></p>
            </div>
        </div>
        <div id="row4" style="display: flex;">
            <div class="details" style="background: #63ab66;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-bookmark-o"></span>
               <p><h3><?php echo $saved; ?></h3> <? echo lang('saved_posts'); ?></p>
            </div>
            <div class="details" style="background: #63ab66;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-lock"></span>
               <p><h3><?php echo $notes; ?></h3> <? echo lang('notes'); ?></p>
            </div>
        </div>
        <div id="row5" style="display: flex;">
            <div class="details" style="background: #2196f3;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-check-circle"></span>
               <p><h3><?php echo $verified; ?></h3> <? echo lang('verified_users'); ?></p>
            </div>
            <div class="details" style="background: #2196f3;">
            <span style='<? echo lang('float2'); ?>:10%;<? echo lang('float'); ?>:unset;' class="fa fa-user-o"></span>
               <p><h3><?php echo $admins; ?></h3> <? echo lang('admins'); ?></p>
            </div>
        </div>
    </div>
    <?php ; break; ?>
    <!--///////////////verify badge/////////////////////-->
    <?php case 'verify': ?>
    <p class="dashboard_path"><a href="?adb=General"><? echo lang('dashboard'); ?></a> / <? echo lang('verify_badge'); ?></p>
    <br>
    <form action="" method="post">
        <p><input type="text" name="verifyBadge" placeholder="@<? echo lang('username'); ?>" class="dashboardField" /></p>
        <br>
        <p>
            <select name="verifyOptions" class="dashboardField">
                <option selected="selected"><? echo lang('verify_user'); ?></option>
                <option><? echo lang('remove_verifyBadge'); ?></option>
            </select>
        </p><br>
        <p><input type="submit" name="verifyBadgeBtn" class="btn_blue" value="<? echo lang('submit'); ?>" /></p>
        <?php echo $verifyBadgeResult; ?>
    </form>
    <?php ; break; ?>
    <!--///////////////Users/////////////////////-->
    <?php case 'Users': ?>
    <p class="dashboard_path"><a href="?adb=General"><? echo lang('dashboard'); ?></a> / <? echo lang('users'); ?></p>
    <br/>
    <?php
    $fetchUsers_sql = "SELECT Username,Fullname,Userphoto FROM signup";
    $fetchUsers = $conn->prepare($fetchUsers_sql);
    $fetchUsers->execute();
    while ($rows = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <table class="dashboard_UsersTable">
        <tr>
        <td style="width: 40px;">
            <div><img src="<?php echo $dircheckPath; ?>imgs/user_imgs/<?php echo $rows['Userphoto']; ?>"></div>
        </td>
        <td>
            <a href="<?php echo $dircheckPath; ?>u/<?php echo $rows['Username']; ?>"><p><?php echo $rows['Fullname']; ?></p></a>
        </td>
        <td align="center" style="width: 150px;"><a href="user?ed=<?php echo $rows['Username']; ?>" class="dashboard_EditDelete"><? echo lang('edit_delete_dashboard'); ?></a></td>
        </tr>
    </table>
    <?php }; break; ?>
    <!--///////////////Support box/////////////////////-->
    <?php case 'Support_box': ?>
    <p class="dashboard_path"><a href="?adb=General"><? echo lang('dashboard'); ?></a> / <? echo lang('supportBox'); ?></p>
    <br>
    <table class="dashboard_sbFetch">
        <tr style="background: #f7f7f7;">
            <td>#</td>
            <td><? echo lang('subject'); ?></td>
            <td><? echo lang('from'); ?></td>
            <td><? echo lang('time'); ?></td>
        </tr>
        <?php
        $n=0;
        $status = "0";
        $fetchsubjects = $conn->prepare("SELECT r_id,from_id,r_type,subject,r_time FROM supportbox WHERE status = :status");
        $fetchsubjects->bindParam(':status',$status,PDO::PARAM_INT);
        $fetchsubjects->execute();
        $rCount = $fetchsubjects->rowCount();
        if ($rCount > 0) {
        while ($fetchR_Rows = $fetchsubjects->fetch(PDO::FETCH_ASSOC)) {
        $replay_id = $fetchR_Rows['r_id'];
        $from_id = $fetchR_Rows['from_id'];
        $subject = $fetchR_Rows['subject'];
        $r_type = $fetchR_Rows['r_type'];
        $r_time = $fetchR_Rows['r_time'];
        switch ($r_type) {
            case 'problem':
                $subject = $subject;
            break;
            case 'post':
                $subject = lang('report_about_post');
            break;

        }
        $fetchFrom = $conn->prepare("SELECT Fullname,Username FROM signup WHERE id = :from_id");
        $fetchFrom->bindParam(':from_id',$from_id,PDO::PARAM_INT);
        $fetchFrom->execute();
        while ($from_Rows = $fetchFrom->fetch(PDO::FETCH_ASSOC)) {
        $fromid_name = $from_Rows['Fullname'];
        $fromid_un = $from_Rows['Username'];
        }
        ?>
        <tr>
            <td><? echo $n+=1; ?></td>
            <td><a href="sbox_r?rid=<? echo $replay_id; ?>"><? echo $subject; ?></a></td>
            <td><a href="<? echo $dircheckPath.'u/'.$fromid_un; ?>"><? echo $fromid_name; ?></a></td>
            <td><? echo time_ago($r_time); ?></td>
        </tr>
        <?php } }else{echo lang('nothingToShow');} ?>
    </table>
    <?php ; break; ?>
    <!--///////////////////////////////////////////////-->
    <?php default: echo lang('nothingToShow');; break; } ?>
    </div>
</div>
</div>
<?php include "../includes/endJScodes.php"; ?>
</body>
</html>