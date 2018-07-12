<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("config/connect.php");
include("includes/fetch_users_info.php");
include ("includes/time_function.php");
include("includes/country_name_function.php");
if(!isset($_SESSION['Username'])){
    header("location: login.php");
}
$tc = filter_var(htmlentities($_GET['tc']),FILTER_SANITIZE_STRING);
// =============================[ prepare input variable's ]=================================
$session_un = $_SESSION['Username'];

$fullname_var = filter_var(htmlentities($_POST['edit_fullname']),FILTER_SANITIZE_STRING);
$username_var = filter_var(htmlentities($_POST['edit_username']),FILTER_SANITIZE_STRING);
$email_var = filter_var(htmlentities($_POST['edit_email']),FILTER_SANITIZE_STRING);
// =========================== password hashinng ==================================
$new_password_var_field = filter_var(htmlentities($_POST['new_pass']),FILTER_SANITIZE_STRING);
$options = array(
    'cost' => 12,
);
$new_password_var = password_hash($new_password_var_field, PASSWORD_BCRYPT, $options);
// ================================================================================
$rewrite_new_password_var = filter_var(htmlentities($_POST['rewrite_new_pass']),FILTER_SANITIZE_STRING);

// filter gender as prefered language
$gender_var = filter_var(htmlentities($_POST['gender']),FILTER_SANITIZE_STRING);
if ($gender_var == lang('male')) {
   $gender_var = "Male";
}elseif ($gender_var == lang('female')) {
    $gender_var = "Female";
}

$school_var = filter_var(htmlentities($_POST['edit_school']),FILTER_SANITIZE_STRING);
$work_var = filter_var(htmlentities($_POST['edit_work']),FILTER_SANITIZE_STRING);
$work0_var = filter_var(htmlentities($_POST['edit_work0']),FILTER_SANITIZE_STRING);
$country_var = filter_var(htmlentities($_POST['edit_country']),FILTER_SANITIZE_STRING);
$birthday_var = filter_var(htmlentities($_POST['bd_year']),FILTER_SANITIZE_NUMBER_INT)."/".filter_var(htmlentities($_POST['bd_month']),FILTER_SANITIZE_NUMBER_INT)."/".filter_var(htmlentities($_POST['bd_day']),FILTER_SANITIZE_NUMBER_INT);
$website_var = filter_var(htmlentities($_POST['edit_website']),FILTER_SANITIZE_STRING);
$bio_var = filter_var(htmlentities($_POST['edit_bio']),FILTER_SANITIZE_STRING);

$language_var = filter_var(htmlspecialchars($_POST['edit_language']),FILTER_SANITIZE_STRING);

$general_current_pass_var = filter_var(htmlentities($_POST['general_current_pass']),FILTER_SANITIZE_STRING);
$EditProfile_current_pass_var = filter_var(htmlentities($_POST['EditProfile_current_pass']),FILTER_SANITIZE_STRING);
$lang_current_pass_var = filter_var(htmlentities($_POST['lang_current_pass']),FILTER_SANITIZE_STRING);
$remeveA_current_pass_var = filter_var(htmlentities($_POST['removeA_current_pass']),FILTER_SANITIZE_STRING);

// =============================[ Save General settings ]=================================
if (isset($_POST['general_save_changes'])) {
if (!password_verify($general_current_pass_var,$_SESSION['Password'])) {
    $general_save_result = "<p class='alertRed'>".lang('current_password_is_incorrect')."</p>";
}else{
    if (empty($fullname_var) or empty($username_var) or empty($email_var)) {
        $general_save_result = "<p class='alertRed'>".lang('please_fill_required_fields')."</p>";
    } else {
         if (empty($new_password_var) AND empty($rewrite_new_password_var)) {
            $new_password_var = $_SESSION['Password'];
         }elseif ($new_password_var_field != $rewrite_new_password_var) {
            $general_save_result = "<p class='alertRed'>".lang('new_password_doesnt_match_the_confirm_field')."</p>";
            $stop = "1";
        }
        if(strpos($username_var, ' ') !== false || preg_match('/[\'^£$%&*()}{@#~?><>,.|=+¬-]/', $username_var) || !preg_match('/[A-Za-z0-9]+/', $username_var)) {
            $general_save_result =  "
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
        $unExist = $conn->prepare("SELECT Username FROM signup WHERE Username =:username_var");
        $unExist->bindParam(':username_var',$username_var,PDO::PARAM_STR);
        $unExist->execute();
        $unExistCount = $unExist->rowCount();
        if ($unExistCount > 0) {
           if ($username_var != $_SESSION['Username']) {
           $general_save_result = "<p class='alertRed'>".lang('user_already_exist')."</p>";
           $stop = "1";
           }
        }
        $emExist = $conn->prepare("SELECT Email FROM signup WHERE Email =:email_var");
        $emExist->bindParam(':email_var',$email_var,PDO::PARAM_STR);
        $emExist->execute();
        $emExistCount = $emExist->rowCount();
        if ($emExistCount > 0) {
           if ($email_var != $_SESSION['Email']) {
           $general_save_result = "<p class='alertRed'>".lang('email_already_exist')."</p>";
           $stop = "1";
           }
        }
        if (!filter_var($email_var, FILTER_VALIDATE_EMAIL)) {
            $general_save_result = "<p class='alertRed'>".lang('invalid_email_address')."</p>";
            $stop = "1";
        }
         if ($stop != "1") {
         $update_info_sql = "UPDATE signup SET Fullname= :fullname_var,Username= :username_var,Email= :email_var,Password= :new_password_var,gender= :gender_var WHERE username= :session_un";
         $update_info = $conn->prepare($update_info_sql);
         $update_info->bindParam(':fullname_var',$fullname_var,PDO::PARAM_STR);
         $update_info->bindParam(':username_var',$username_var,PDO::PARAM_STR);
         $update_info->bindParam(':email_var',$email_var,PDO::PARAM_STR);
         $update_info->bindParam(':new_password_var',$new_password_var,PDO::PARAM_STR);
         $update_info->bindParam(':gender_var',$gender_var,PDO::PARAM_STR);
         $update_info->bindParam(':session_un',$session_un,PDO::PARAM_STR);
         $update_info->execute();
        if (isset($update_info)) {
            $_SESSION['Fullname'] = $fullname_var;
            $_SESSION['Username'] = $username_var;
            $_SESSION['Email'] = $email_var;
            $_SESSION['Password'] = $new_password_var;
            $_SESSION['gender'] = $gender_var;
            $general_save_result = "<p class='alertGreen'>".lang('changes_saved_seccessfully')."</p>";
        } else {
            $general_save_result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
        }
        }
    }
}
}
// =============================[ Save Edit profile settings ]==============================
if (isset($_POST['EditProfile_save_changes'])) {
if (!password_verify($EditProfile_current_pass_var,$_SESSION['Password'])) {
    $EditProfile_save_result = "<p class='alertRed'>".lang('current_password_is_incorrect')."</p>";
}else{
    $update_info_sql = "UPDATE signup SET school= :school_var,work0= :work0_var,work= :work_var,country= :country_var,birthday= :birthday_var,website= :website_var,bio= :bio_var WHERE username= :session_un";
     $update_info = $conn->prepare($update_info_sql);
     $update_info->bindParam(':school_var',$school_var,PDO::PARAM_STR);
     $update_info->bindParam(':work0_var',$work0_var,PDO::PARAM_STR);
     $update_info->bindParam(':work_var',$work_var,PDO::PARAM_STR);
     $update_info->bindParam(':country_var',$country_var,PDO::PARAM_STR);
     $update_info->bindParam(':birthday_var',$birthday_var,PDO::PARAM_STR);
     $update_info->bindParam(':website_var',$website_var,PDO::PARAM_STR);
     $update_info->bindParam(':bio_var',$bio_var,PDO::PARAM_STR);
     $update_info->bindParam(':session_un',$session_un,PDO::PARAM_STR);
     $update_info->execute();
    if (isset($update_info)) {
        $_SESSION['school'] = $school_var;
        $_SESSION['work0'] = $work0_var;
        $_SESSION['work'] = $work_var;
        $_SESSION['country'] = $country_var;
        $_SESSION['birthday'] = $birthday_var;
        $_SESSION['website'] = $website_var;
        $_SESSION['bio'] = $bio_var;
        $EditProfile_save_result = "<p class='alertGreen'>".lang('changes_saved_seccessfully')."</p>";
    } else {
        $EditProfile_save_result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
    }

}
}
// =============================[ Save Languages settings ]=================================
if (isset($_POST['lang_save_changes'])) {
if (!password_verify($lang_current_pass_var,$_SESSION['Password'])) {
    $lang_save_result = "<p class='alertRed'>".lang('current_password_is_incorrect')."</p>";
}else{
     $update_info_sql = "UPDATE signup SET language= :language_var WHERE username= :session_un";
     $update_info = $conn->prepare($update_info_sql);
     $update_info->bindParam(':language_var',$language_var,PDO::PARAM_STR);
     $update_info->bindParam(':session_un',$session_un,PDO::PARAM_STR);
     $update_info->execute();
    if (isset($update_info)) {
        $_SESSION['language'] = $language_var;
        $lang_save_result = "<p class='alertGreen'>".lang('changes_saved_seccessfully')."</p>";
    } else {
        $lang_save_result = "<p class='alertRed'>".lang('errorSomthingWrong')."</p>";
    }
}
}
// =============================[ Remove account ]=================================
$myid = $_SESSION['id'];
if (isset($_POST['removeA_save_changes'])) {
if (!password_verify($remeveA_current_pass_var,$_SESSION['Password'])) {
    $removeA_save_result = "<p class='alertRed'>".lang('current_password_is_incorrect')."</p>";
}else{
     $remeveAccount_sql = "DELETE FROM signup WHERE Username= :session_un";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':session_un',$session_un,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM comments WHERE c_author_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM follow WHERE uf_one= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM follow WHERE uf_two= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM likes WHERE liker= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM mynotepad WHERE author_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM r_star WHERE u_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM r_star WHERE p_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM wpost WHERE author_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM notifications WHERE from_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM saved WHERE user_saved_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     $remeveAccount_sql = "DELETE FROM supportbox WHERE from_id= :myid";
     $remeveAccount = $conn->prepare($remeveAccount_sql);
     $remeveAccount->bindParam(':myid',$myid,PDO::PARAM_STR);
     $remeveAccount->execute();
     header("location: login");
}
}
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title>Account settings | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="settings,social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <?php include "includes/head_imports_main.php";?>
</head>
<body" style="overflow-y: scroll">
<!--=============================[ NavBar ]========================================-->
<?php include "includes/navbar_main.php"; ?>
<!--=============================[ Div_Container ]========================================-->
<div class="main_container">
    <center>
<div class="settings" style="text-align:<?php echo lang('textAlign'); ?>;">
<div style="width: 300px;background: white;margin: 5px;border-radius: 3px;box-shadow: 0px 0px 18px rgba(63, 81, 181, 0.16);">
<ul class="tab" id="settings_ultab">
    <li>
        <a href="?tc=general" id="a_general">
            <p class="tablinks <?php if($tc == 'general' or $tc == ''){echo 'tablinksActive';} ?>"><span class="fa fa-cogs"></span> <?php echo lang('general'); ?></p>
        </a>
    </li>

    <li>
        <a href="?tc=edit_profile" id="a_edit_profile">
            <p class="tablinks <?php if($tc == 'edit_profile'){echo 'tablinksActive';} ?>"><span class="fa fa-user-o"></span> <?php echo lang('edit_profile'); ?></p>
        </a>
    </li>

    <li>
        <a href="?tc=language" id="a_language">
            <p class="tablinks <?php if($tc == 'language'){echo 'tablinksActive';} ?>"><span class="fa fa-language"></span> <?php echo lang('language'); ?></p>
        </a>
    </li>

    <li>
        <a href="?tc=remove_account" id="a_removeA">
            <p class="tablinks <?php if($tc == 'remove_account'){echo 'tablinksActive';} ?>"><span class="fa fa-trash"></span> <?php echo lang('remove_account'); ?></p>
        </a>
    </li>
</ul>
</div>
<!--====================[ Edit profile section ]======================-->
<?php switch ($tc) { ?>
<?php case 'edit_profile': ?>
<div id="Edit_profile" class="tabcontent" style="height: auto;">
    <p align="center" id="about_save_result"><?php echo $EditProfile_save_result; ?></p>
    <form action="" method="post">
    <p>
    <p class="settings_fieldTitle"><?php echo lang('education'); ?></p>
    <input dir="auto" class="settings_textfield" type="text" name="edit_school" value="<?php echo $_SESSION['school']; ?>" /></p>
    <p>
    <p class="settings_fieldTitle"><?php echo lang('work'); ?></p>
    <input dir="auto" class="settings_textfield" type="text" name="edit_work0" placeholder="<?php echo lang('work_title'); ?>" style="width: 140px;" value="<?php echo $_SESSION['work0']; ?>" /><span class="settings_tf_mergeSpan"><?php echo lang('at'); ?></span><input dir="auto" class="settings_textfield" type="text" name="edit_work" placeholder="<?php echo lang('work_place'); ?>" style="width: 140px;" value="<?php echo $_SESSION['work']; ?>" /></p>
    <p>
    <p class="settings_fieldTitle"><?php echo lang('country'); ?></p>
    <select  class="settings_textfield" name="edit_country">
        <option selected disabled hidden><?php
        if (!empty($_SESSION['country'])) {echo $_SESSION['country'];}else{echo "Select your country";}?></option>
        <?php foreach($countries as $key => $value) { ?>
        <option <?php if($_SESSION['country'] == "$value"){ echo "selected";} ?> value="<?= htmlspecialchars($value) ?>" title="<?= $key ?>"><?= htmlspecialchars($value) ?></option>
        <?php } ?>
    </select></p>
        <p>
        <p class="settings_fieldTitle"><?php echo lang('birthday'); ?></p>
        <?php
        $exBthD = explode("/", $_SESSION['birthday']);
        echo '<select name="bd_year" class="settings_textfield" style="width: 97px;">';
            for($i = date('Y'); $i >= date('Y', strtotime('-100 years')); $i--){
              if ($exBthD[0] == $i) {
                 echo "<option selected='selected' value='$i'>$i</option>";
              }else{
                echo "<option value='$i'>$i</option>";
              }
            } 
        echo '</select> ';
        echo '<select name="bd_month" class="settings_textfield" style="width: 97px;">';
            for($i = 1; $i <= 12; $i++){
              $i = str_pad($i, 2, 0, STR_PAD_LEFT);
              if ($exBthD[1] == $i) {
                 echo "<option selected='selected' value='$i'>$i</option>";
              }else{
                echo "<option value='$i'>$i</option>";
              }
            }
        echo '</select> ';
        echo '<select name="bd_day" class="settings_textfield" style="width: 97px;">';
            for($i = 1; $i <= 31; $i++){
              $i = str_pad($i, 2, 0, STR_PAD_LEFT);
              if ($exBthD[2] == $i) {
                 echo "<option selected='selected' value='$i'>$i</option>";
              }else{
                echo "<option value='$i'>$i</option>";
              }
            }
        echo '</select> ';
        ?>
        </p>
        <p>
        <p class="settings_fieldTitle"><?php echo lang('website'); ?></p>
        <input dir="auto" class="settings_textfield" type="text" name="edit_website" value="<?php echo $_SESSION['website']; ?>" /></p>
        <p>
        <p class="settings_fieldTitle"><?php echo lang('bio'); ?></p>
        <textarea style="resize:none;height: 100px" dir="auto" class="settings_textfield" placeholder="150" maxlength="150" type="text" name="edit_bio"><?php echo $_SESSION['bio']; ?></textarea></p>
        <p>
        <div style="background: #e9ebee; border-radius: 3px; padding: 15px;">
         <p><input class="settings_textfield" type="password" name="EditProfile_current_pass" placeholder="<?php echo lang('current_password'); ?>" style="background: #fff;" /></p>
         <p style="margin: 0;"><input class="btn_blue" name="EditProfile_save_changes" type="submit" value="<?php echo lang('save_changes'); ?>" /></p>
         </div>
    </form>
</div>
<?php break; ?>
<!--====================[ Languages section ]======================-->
<?php case 'language': ?>
<div id="Language" class="tabcontent" style="height: auto;">
         <p align="center" id="lang_save_result"><?php echo $lang_save_result; ?></p>
    <form action="" method="post">
        <p>
        <select class="settings_textfield" name="edit_language">
            <option <?php if($_SESSION['language'] == "English"){ echo "selected";} ?> >English</option>
        <option <?php if($_SESSION['language'] == "العربية"){ echo "selected";} ?> >العربية</option>
        </select>
        </p>
        <div style="background: #e9ebee; border-radius: 3px; padding: 15px;">
         <p><input class="settings_textfield" type="password" name="lang_current_pass" placeholder="<?php echo lang('current_password'); ?>" style="background: #fff;" /></p>
         <p style="margin: 0;"><input class="btn_blue" name="lang_save_changes" type="submit" value="<?php echo lang('save_changes'); ?>" /></p>
         </div>
    </form>
</div>
<?php break; ?>
<!--====================[ Remove account section ]======================-->
<?php case 'remove_account': ?>
<div id="remove_account" class="tabcontent" style="height: auto;">
<form action="" method="post">
    <div style="background: #e9ebee; border-radius: 3px; padding: 15px;">
    <p style="background: rgba(247, 81, 81, 0.14); color: #f75151; padding: 15px; border: 1px solid #f75151; border-radius: 3px;"><?php echo lang('remove_account_note'); ?></p>
    <p><input class="settings_textfield" type="password" name="removeA_current_pass" placeholder="<?php echo lang('current_password'); ?>" style="background: #fff;" /></p>
    <p style="margin: 0;"><input class="red_flat_btn" name="removeA_save_changes" type="submit" value="<?php echo lang('remove_account'); ?>" /></p>
    </div>
    <p align="center" id="removeA_save_result"><?php echo $removeA_save_result; ?></p>
</form>
</div>
<?php break; ?>
<!--====================[ General section ]======================-->
<?php default: ?>
    <div id="General" class="tabcontent" style="height: auto;">
         <p align="center" id="general_save_result"><?php echo $general_save_result; ?></p>
        <form action="" method="post">
            <p>
            <p class="settings_fieldTitle"><?php echo lang('fullname'); ?> <span><?php echo lang('required'); ?></span></p>
            <input dir="auto" class="settings_textfield" type="text" name="edit_fullname" value="<?php echo $_SESSION['Fullname']; ?>" /></p>
            <p>
            <p class="settings_fieldTitle"><?php echo lang('username'); ?> <span><?php echo lang('required'); ?></span></p>
            <input dir="auto" class="settings_textfield" type="text" name="edit_username" value="<?php echo $_SESSION['Username']; ?>" /></p>
            <p>
            <p class="settings_fieldTitle"><?php echo lang('email'); ?> <span><?php echo lang('required'); ?></span></p>
            <input dir="auto" class="settings_textfield" type="text" name="edit_email" value="<?php echo $_SESSION['Email']; ?>" /></p>
            <p>
            <p class="settings_fieldTitle"><?php echo lang('new_password'); ?></p>
            <input class="settings_textfield" type="password" name="new_pass" /></p>
            <p>
            <p class="settings_fieldTitle"><?php echo lang('confirm_new_password'); ?></p>
            <input class="settings_textfield" type="password" name="rewrite_new_pass" /></p>
             <p>
            <p class="settings_fieldTitle"><?php echo lang('gender'); ?> <span><?php echo lang('required'); ?></span></p>
             <select class="settings_textfield" name="gender">
             <option <?php if($_SESSION['gender'] == "Male"){ echo "selected";} ?> ><?php echo lang('male'); ?></option>
             <option <?php if($_SESSION['gender'] == "Female"){ echo "selected";} ?> ><?php echo lang('female'); ?></option>
             </select>
             </p>
             <div style="background: #e9ebee; border-radius: 3px; padding: 15px;">
                 <p><input class="settings_textfield" type="password" name="general_current_pass" placeholder="<?php echo lang('current_password'); ?>" style="background: #fff;" /></p>
                <p style="margin: 0;"><input class="btn_blue" name="general_save_changes" type="submit" value="<?php echo lang('save_changes'); ?>" /></p>
             </div>
            </form>
    </div>
<?php /*//////////////////////////////////////////////////////*/ break; } ?>
</div>
    </center>
    </div>
    <!--===============================[ End ]==========================================-->
    <?php include("includes/footer.php");?>
</body>
</html>