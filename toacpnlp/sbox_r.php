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

// this to make dashboard 'Users' link active
$urlP='Support_box';

// set check path var
if (is_dir("imgs/")) {
    $dircheckPath = "";
}elseif (is_dir("../imgs/")) {
    $dircheckPath = "../";
}elseif (is_dir("../../imgs/")) {
    $dircheckPath = "../../";
}

//get request 'rid'
$rid = trim(filter_var(htmlentities($_GET['rid']),FILTER_SANITIZE_NUMBER_INT));
$replayInput = trim(filter_var(htmlentities($_POST['replayR']),FILTER_SANITIZE_STRING));

// if replay btn pressed
if (isset($_POST['submitR'])) {
    if (!empty($replayInput)) {
        $status = "1";
        $replay_time = time();
        $sendReplay = $conn->prepare("UPDATE supportbox SET r_replay = :r_replay,r_replay_time = :r_replay_time,status = :status WHERE r_id = :rid");
        $sendReplay->bindParam(':r_replay',$replayInput,PDO::PARAM_STR);
        $sendReplay->bindParam(':r_replay_time',$r_replay_time,PDO::PARAM_INT);
        $sendReplay->bindParam(':status',$status,PDO::PARAM_INT);
        $sendReplay->bindParam(':rid',$rid,PDO::PARAM_INT);
        $sendReplay->execute();
        if ($sendReplay) {
            header("location: index?adb=Support_box");
        }else{
            $result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
        }
    }else{
        $result = "<p class='alertRed'>".lang('please_fill_required_fields')."</p>";
    }
}


?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><? echo lang('dashboard'); ?> | Wallstant</title>
    <admin charset="UTF-8">
    <admin name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
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
    <!--///////////////User Actions/////////////////////-->
    <p class="dashboard_path"><a href="index?adb=General"><? echo lang('dashboard'); ?></a> / <a href="index?adb=Support_box"><? echo lang('supportBox'); ?></a> / <? echo lang('replay'); ?></p>
    <br>
   <?php
        $n=0;
        $fetchReports = $conn->prepare("SELECT r_id,for_id,from_id,r_type,report,r_time FROM supportbox WHERE r_id =:rid");
        $fetchReports->bindParam(':rid',$rid,PDO::PARAM_INT);
        $fetchReports->execute();
        while ($fetchR_Rows = $fetchReports->fetch(PDO::FETCH_ASSOC)) {
        $replay_id = $fetchR_Rows['r_id'];
        $for_id = $fetchR_Rows['for_id'];
        $from_id = $fetchR_Rows['from_id'];
        $report = $fetchR_Rows['report'];
        $r_type = $fetchR_Rows['r_type'];
        $r_time = time_ago($fetchR_Rows['r_time']);
        switch ($r_type) {
            case 'problem':
                $report = $report;
            break;
            case 'post':
                $report = "<a href='".$dircheckPath."posts/post?pid=$for_id'>".lang('report_about_post')."</a>";
            break;
        }
        $fetchFrom = $conn->prepare("SELECT Fullname,Username,Userphoto FROM signup WHERE id = :from_id");
        $fetchFrom->bindParam(':from_id',$from_id,PDO::PARAM_INT);
        $fetchFrom->execute();
        while ($from_Rows = $fetchFrom->fetch(PDO::FETCH_ASSOC)) {
        $fromid_name = $from_Rows['Fullname'];
        $fromid_un = $from_Rows['Username'];
        $fromid_ph = $from_Rows['Userphoto'];
        }
        ?>
        <? echo $result; ?>
        <table class="sbox_r_table">
            <tr>
                <td>
                    <div style="width: 32px;height: 32px;overflow: hidden;border-radius: 20px;border:1px solid #ccc;">
                        <img style="width: auto;height: 100%;" src="<? echo $dircheckPath; ?>imgs/user_imgs/<? echo $fromid_ph; ?>" />
                    </div>
                </td>
                <td><a href="<? echo $dircheckPath.'u/'.$fromid_un; ?>"><? echo $fromid_name; ?></a></td>
            </tr>
            <tr>
                <td></td>
                <td>
                <p class="sbox_r_report"><? echo $report; ?></p>
                <? echo "<span style='font-size: 11px;color:gray;margin:8px;'>$r_time</span>"; ?>
                </td>
            </tr>
            <form action="" method="post">
            <tr>
                <td></td>
                <td><textarea name="replayR" style="resize:none;width:370px;height: 40px;" class="dashboardField"></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn_blue" name="submitR" value="<? echo lang('replay'); ?>" /></td>
            </tr>
            </form>
        </table>
        <?php } ?>
    <!--///////////////////////////////////////////////-->
    </div>
</div>
</div>
<script type="text/javascript">
    $('.dashboardField').each(function () {
      this.setAttribute('style', 'height:40px;overflow-y:hidden;resize:none;width:370px;text-align:'+"<?php echo lang('textAlign'); ?>"+';');
    }).on('input', function () {
      this.style.height = '40px';
      this.style.height = (this.scrollHeight) + 'px';
    });
</script>
<?php include "../includes/endJScodes.php"; ?>
</body>
</html>
