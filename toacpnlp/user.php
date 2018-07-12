<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include ("../includes/num_k_m_count.php");

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
$urlP='Users';

// set check path var
if (is_dir("imgs/")) {
    $dircheckPath = "";
}elseif (is_dir("../imgs/")) {
    $dircheckPath = "../";
}elseif (is_dir("../../imgs/")) {
    $dircheckPath = "../../";
}
// get var's
$ed = trim(filter_var(htmlspecialchars($_GET['ed']),FILTER_SANITIZE_STRING));
$db_fullname = trim(filter_var(htmlentities($_POST['fullname']),FILTER_SANITIZE_STRING));
$db_username = trim(filter_var(htmlentities($_POST['username']),FILTER_SANITIZE_STRING));
$db_email = trim(filter_var(htmlentities($_POST['email']),FILTER_SANITIZE_STRING));
// =========================== password hashinng ==================================
$db_password_var = trim(filter_var(htmlentities($_POST['password']),FILTER_SANITIZE_STRING));
$options = array(
    'cost' => 12,
);
$db_password = password_hash($db_password_var, PASSWORD_BCRYPT, $options);
// ================================================================================
$db_admin = trim(filter_var(htmlentities($_POST['admin']),FILTER_SANITIZE_STRING));
switch ($db_admin) {
    case lang('yes'):
        $db_admin = "2";
        break;
    case lang('no'):
        $db_admin = "0";
        break;
}
// get information of username and put it into fields as default 
$uInfo = $conn->prepare("SELECT id,Fullname,Username,Email,Password,admin FROM signup WHERE Username = :ed");
$uInfo->bindParam(':ed',$ed,PDO::PARAM_STR);
$uInfo->execute();
$uInfo_count = $uInfo->rowCount();
if ($uInfo_count > 0) {
while ($uInfoRow = $uInfo->fetch(PDO::FETCH_ASSOC)) {
    $uInfo_id = $uInfoRow['id'];
    $uInfo_fn = $uInfoRow['Fullname'];
    $uInfo_un = $uInfoRow['Username'];
    $uInfo_em = $uInfoRow['Email'];
    $uInfo_pd = $uInfoRow['Password'];
    $uInfo_ad = $uInfoRow['admin'];
}
}else{
    $un_not_found = "user not found";
}
// update user info
if (isset($_POST['submit_uInfo'])) {
    if (empty($db_fullname) or empty($db_username) or empty($db_email)) {
        $update_result = "<p class='alertRed'>".lang('please_fill_required_fields')."</p>";
        $stop = "1";
    }
    if(strpos($db_username, ' ') !== false || preg_match('/[\'^£$%&*()}{@#~?><>,.|=+¬-]/', $db_username) || !preg_match('/[A-Za-z0-9]+/', $db_username)) {
        $update_result = "
            <ul class='alertRed' style='list-style:none;'>
                <li><b>".lang('username_not_allowed')." :</b></li>
                <li><span class='fa fa-times'></span> ".lang('signup_username_should_be_1').".</li>
                <li><span class='fa fa-times'></span> ".lang('signup_username_should_be_2').".</li>
                <li><span class='fa fa-times'></span> ".lang('signup_username_should_be_3').".</li>
                <li><span class='fa fa-times'></span> ".lang('signup_username_should_be_4').".</li>
                <li><span class='fa fa-times'></span> ".lang('signup_username_should_be_5').".</li>
            </ul>";
        $stop = "1";
    }
    // check if username exist
    $unExist = $conn->prepare("SELECT Username FROM signup WHERE Username =:db_username");
    $unExist->bindParam(':db_username',$db_username,PDO::PARAM_STR);
    $unExist->execute();
    $unExistCount = $unExist->rowCount();
    if($unExistCount > 0){
        if ($ed != $db_username) {
        $update_result = "<p class='alertRed'>".lang('user_already_exist')."</p>";
        $stop = "1";
        }
    }
    // check if email exist
    $emExist = $conn->prepare("SELECT Email FROM signup WHERE Email =:db_email");
    $emExist->bindParam(':db_email',$db_email,PDO::PARAM_STR);
    $emExist->execute();
    $emExistCount = $emExist->rowCount();
    if($emExistCount > 0){
        if ($uInfo_em != $db_email) {
        $update_result = "<p class='alertRed'>".lang('email_already_exist')."</p>";
        $stop = "1";
        }
    }
    if (!filter_var($db_email, FILTER_VALIDATE_EMAIL)) {
        $update_result = "<p class='alertRed'>".lang('invalid_email_address')."</p>";
        $stop = "1";
    }
    if ($stop != "1") {
        if (empty($db_password_var)) {
        $update = $conn->prepare("UPDATE signup SET Fullname = :db_fullname,Username = :db_username,Email = :db_email,admin = :db_admin WHERE Username = :ed");
        }else{
        $update = $conn->prepare("UPDATE signup SET Fullname = :db_fullname,Username = :db_username,Email = :db_email,Password = :db_password,admin = :db_admin WHERE Username = :ed");
        }
        $update->bindParam(':db_fullname',$db_fullname,PDO::PARAM_STR);
        $update->bindParam(':db_username',$db_username,PDO::PARAM_STR);
        $update->bindParam(':db_email',$db_email,PDO::PARAM_STR);
        if (!empty($db_password_var)) {
        $update->bindParam(':db_password',$db_password,PDO::PARAM_STR);            
        }
        $update->bindParam(':db_admin',$db_admin,PDO::PARAM_INT);
        $update->bindParam(':ed',$ed,PDO::PARAM_STR);
        $update->execute();
        if ($update) {
            $update_result = "<p class='alertGreen'>".lang('changes_saved_seccessfully')."</p>";
        }else{
            $update_result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
        }
    }
}


// remove a user from all tables (forever from database)
if (isset($_POST['rAccBtn'])) {
    if (empty($_POST['rAccField'])) {
        $update_result = "<p class='alertRed'>".lang('current_password_is_incorrect')."</p>";
    }else{
        $remeveAccount_sql = "DELETE FROM signup WHERE id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM comments WHERE c_author_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM follow WHERE uf_one= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM follow WHERE uf_two= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM likes WHERE liker= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM mynotepad WHERE author_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM r_star WHERE u_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM r_star WHERE p_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM wpost WHERE author_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM notifications WHERE from_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM saved WHERE user_saved_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        $remeveAccount_sql = "DELETE FROM supportbox WHERE from_id= :uInfo_id";
        $remeveAccount = $conn->prepare($remeveAccount_sql);
        $remeveAccount->bindParam(':uInfo_id',$uInfo_id,PDO::PARAM_STR);
        $remeveAccount->execute();
        if ($remeveAccount) {
            header("location: index?adb=Users");
        }else{
            $update_result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
        }
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
    <? if ($un_not_found != "user not found") {
    ?>
    <p class="dashboard_path"><a href="index?adb=General"><? echo lang('dashboard'); ?></a> / <a href="index?adb=Users"><? echo lang('users'); ?></a> / <? echo lang('edit_delete_dashboard'); ?></p>
    <br>
    <?php
    $admin_int = "1";
    $chAdmin = $conn->prepare("SELECT Username FROM signup WHERE Username =:ed AND admin =:admin_int");
    $chAdmin->bindParam(':ed',$ed,PDO::PARAM_STR);
    $chAdmin->bindParam(':admin_int',$admin_int,PDO::PARAM_INT);
    $chAdmin->execute();
    $chAdminCount = $chAdmin->rowCount();
    if ($chAdminCount < 1) {
    ?>
    <? echo $update_result; ?>
    <form action="" method="post">
        <table>
            <tr>
                <td><? echo lang('fullname'); ?> :</td>
                <td><input dir="auto" type="text" name="fullname" class="dashboardField" value="<? echo $uInfo_fn ?>"></td>
            </tr>
            <tr>
                <td><? echo lang('username'); ?> :</td>
                <td><input dir="auto" type="text" name="username" class="dashboardField" value="<? echo $uInfo_un ?>"></td>
            </tr>
            <tr>
                <td><? echo lang('email'); ?> :</td>
                <td><input dir="auto" type="email" name="email" class="dashboardField" value="<? echo $uInfo_em ?>"></td>
            </tr>
            <tr>
                <td><? echo lang('password'); ?> :</td>
                <td><input dir="auto" type="password" name="password" class="dashboardField"></td>
            </tr>
            <tr>
                <td><? echo lang('upgradeToAdmin'); ?> :</td>
                <td><select name="admin" class="dashboardField">
                    <option <? if($uInfo_ad == 1){echo "selected";} ?>><? echo lang('yes'); ?></option>
                    <option <? if($uInfo_ad == 0){echo "selected";} ?>><? echo lang('no'); ?></option>
                </select></td>
            </tr>
            <tr>
                <td></td>
                <td><input style="margin: 5px" type="submit" name="submit_uInfo" class="btn_blue" value="<? echo lang('save_changes'); ?>"></td>
            </tr>
        </table>
    </form>
    <hr>
    <form action="" method="post">
        <h4 style="color: #ee4f51;"><?php echo lang('remove_account'); ?></h4>
        <p style="background: rgba(247, 81, 81, 0.14); color: #f75151; padding: 15px; border: 1px solid #f75151; border-radius: 3px;"><?php echo lang('remove_account_note'); ?></p>
        <p style="margin: 8px 0px;"><input class="settings_textfield" type="password" name="rAccField" placeholder="<?php echo lang('current_password'); ?>" style="background: #fff;" /></p>
        <p style="margin: 8px 0px;"><input class="red_flat_btn" name="rAccBtn" type="submit" value="<?php echo lang('remove_account'); ?>" /></p>
    </form>
    <?php
    }else{
        if ($ed == $_SESSION['Username']) {
            echo "<p class='alertYellow'>".lang('uCan_access_your_data_from_settings')."</p>";
        }else{
            echo "<p class='alertRed'>".lang('uCannot_access_admin_data')."</p>";
        }
    }
    
    }else{
        echo "<p class='alertRed'>".lang('username_not_exists')."</p>";
    } ?>
    <!--///////////////////////////////////////////////-->
    </div>
</div>
</div>
<?php include "../includes/endJScodes.php"; ?>
</body>
</html>
