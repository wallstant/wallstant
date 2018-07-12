<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
if(isset($_SESSION['Username'])){
    header("location: home");
}
$getLang = trim(filter_var(htmlentities($_GET['lang']),FILTER_SANITIZE_STRING));
if (!empty($getLang)) {
$_SESSION['language'] = $getLang;
}
// ========================= config the languages ================================
error_reporting(E_NOTICE ^ E_ALL);
if (is_file('home.php')){
    $path = "";
}elseif (is_file('../home.php')){
    $path =  "../";
}elseif (is_file('../../home.php')){
    $path =  "../../";
}
include_once $path."langs/set_lang.php";
?>
<html dir="<? echo lang('html_dir'); ?>">
<head>
    <title><? echo lang('login'); ?> | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="login,social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/head_imports_main.php";?>
</head>
    <body class="login_signup_body">
    <!--============[ Nav bar ]============-->
        <div class="login_signup_navbar">
                <a href="index" class="login_signup_navbarLinks">Wallstant</a>
                <a href="#" class="login_signup_navbarLinks"><? echo lang('help'); ?></a>
                <a href="#" class="login_signup_navbarLinks"><? echo lang('terms'); ?></a>
                <a href="#" class="login_signup_navbarLinks"><? echo lang('privacyPolicy'); ?></a>
                <div style="float: <? echo lang('float2'); ?>;">
                    <a href="login" class="login_signup_btn1"><? echo lang('login'); ?></a>
                    <a href="signup" class="login_signup_btn2"><? echo lang('signup'); ?></a>
                </div>
        </div>
        <!--============[ main contains ]============-->
        <div align="center">
            <div class="login_signup_box2" style="text-align:<? echo lang('textAlign'); ?>">
                <h4 align="center"><? echo lang('login_now'); ?></h4><br/>
                <p><input type="text" name="login_username" id="un" class="login_signup_textfield" placeholder="<? echo lang('email_or_username'); ?>"/></p>
                <p><input type="password" name="login_password" id="pd" class="login_signup_textfield" placeholder="<? echo lang('password'); ?>"/></p>
                <p><a href="#" style="color: #a2a2a2; font-size: 11px; float: <? echo lang('float2'); ?>;"> <? echo lang('forgot_password'); ?></a></p>
                <button type="submit" class="login_signup_btn1" id="loginFunCode"><? echo lang('login'); ?></button>
                <p id="login_wait" style="margin: 0px;"></p>
            </div>
            <div style="background: #fff; border-radius: 3px; max-width: 420px; padding: 15px; margin:auto;margin-top: 15px;color: #7b7b7b;" align="center">
                <? echo lang('dont_have_an_account'); ?> <a href="signup"><? echo lang('signup'); ?></a> <? echo lang('for_free'); ?>.<hr style="margin: 8px;">
                <a href="?lang=english">English</a> &bull; <a href="?lang=العربية">العربية</a>
            </div>
        </div>

<script type="text/javascript">
function loginUser(){
var username = document.getElementById("un").value;
var password = document.getElementById("pd").value;
$.ajax({
type:'POST',
url:'includes/login_signup_codes.php',
data:{'req':'login_code','un':username,'pd':password},
beforeSend:function(){
$('.login_signup_btn1').hide();
$('#login_wait').html("<? echo lang('loading'); ?>...");
},
success:function(data){
$('#login_wait').html(data);
if (data == "Welcome...") {
    $('#login_wait').html("<p class='alertGreen'><? echo lang('welcome'); ?>..</p>");
    setTimeout(' window.location.href = "home"; ',2000);
}else{
    $('.login_signup_btn1').show();
}
},
error:function(err){
alert(err);
}
});
}
$('#loginFunCode').click(function(){
loginUser();
});
$(".login_signup_textfield").keypress( function (e) {
    if (e.keyCode == 13) {
        loginUser();
    }
});
</script>
</body>
</html>
