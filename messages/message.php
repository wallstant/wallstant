<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include("../includes/fetch_users_info.php");
include ("../includes/time_function.php");
if(!isset($_SESSION['Username'])){
    header("location: ../index");
}

$msgId = trim(filter_var(htmlentities($_GET['id'])),FILTER_SANITIZE_NUMBER_INT);
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><? echo lang('messages'); ?> | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="Wallstant is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="Notifications,social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "../includes/head_imports_main.php";?>
</head>
<body>
<!--=============================[ NavBar ]========================================-->
<?php include "../includes/navbar_main.php"; ?>
<!--=============================[ Div_Container ]========================================-->
<div class="messages_container" style="text-align: center;">
    <div style="text-align: <? echo lang('textAlign'); ?>">
    	<div class="messages">
    		<div class="messages_col1">
    		<div class="mCol1_title">
    		<input type="text" class="m_contacts_search" id="mU_search" name="mU_search" placeholder="<? echo lang('search'); ?>" />
    		</div>
    		<div id="m_contacts" class="scrollbar" style="position: absolute; top: 0; right: 0; left: 0; bottom: 0; margin-top: 50px; overflow: auto;">
                <p class="m_contacts_title"><? echo lang('requests'); ?></p>
                <div id="m_contacts_requests">
                    <div style="text-align: center; padding: 15px;"><img src="<? echo $dircheckPath; ?>imgs/loading_video.gif"></div>
                </div>
                <br>
                <p class="m_contacts_title" style="border-top: 1px solid #d0d4d8;"><? echo lang('friends'); ?></p>
                <div id="m_contacts_friends">
                    <div style="text-align: center; padding: 15px;"><img src="<? echo $dircheckPath; ?>imgs/loading_video.gif"></div>
                </div>
    		</div>
    		<div id="m_contacts_search" class="scrollbar" style="display:none;position: absolute; top: 0; right: 0; left: 0; bottom: 0; margin-top: 50px; overflow: auto;"></div>
    		</div>
    		<div class="messages_col2">
    		<div class="mCol2_title" data-user="0">
    			<? echo lang('messages'); ?>
    		</div>
    		<div class="mCol2_msgs scrollbar">
			<div id="m_messages">
			</div>
			<p class="selectToChat">
				<? echo lang('selectToChat'); ?>
			</p>
			<div id="m_userSeen" style="display:none;padding: 0px 8px; color: #545454; font-size: 12px;text-align: right;"><span class="fa fa-check"></span> seen</div>
			<div id="m_userTyping" class="m_msgU1" style="display:none;margin: 8px;margin-bottom: 15px;"><img src="<? echo $dircheckPath; ?>/imgs/typing.gif" style=" width: 30px; "></div>
			<div id="m_messages_loading" style='display:none;text-align: center; padding: 15px;'><img src='<? echo $dircheckPath; ?>imgs/loading_video.gif'></div>
    		</div>
    		<div class="m_SendField_box">
    			<div class="m_SendField">
    				<textarea dir="auto" maxlength="1538" id="mSendField" placeholder="<? echo lang('write_a_message'); ?>"></textarea><span style="<? echo lang('float2'); ?>:0px;<? echo lang('float'); ?>:auto;" class="fa fa-smile-o m_SendField_span" onclick="mEmojiBtn()"></span>
					<div id="emBox" data-emtog="0" style="<? echo lang('float2'); ?>:0px;bottom: 50px;top: auto;" class="emoticonsBox"></div>
    			</div>
    			</div>
    		</div>
    		<div class="messages_col3">
    		<div class="mCol3_title">
    			<? echo lang('user_profile'); ?>
    		</div>
    		<div class="mCol3_userInfo">
    			<div style="position: relative;">
    			<div class="mCol3_userInfo_avatar">
    			</div>
    			<div class="mCol3_userActive" style="background: #ccc;<? echo lang('float2'); ?>:55%;"></div>
    			</div>
    			<h4 style="text-align: center;"><div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></h4>
    			<p style="text-align:center;margin: 0px;color: gray"><div style="width: 40%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></p>
    		</div>
    		<div class="mCol3_bio" style="text-align:<? echo lang('textAlign'); ?>;">
    			<div style="width: 80%; height: 10px; background: rgba(217, 221, 224, 0.55);"></div>
    			<div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55);margin-top: 8px;"></div>
    		</div>
    		</div>
    	</div>
    </div>
</div>
<!--===============================[ End ]==========================================-->
<?php include "../includes/endJScodes.php"; ?>
<script type="text/javascript">
// on click on any user in contacts do this
$('#m_contacts').on("click",".mC_userLink",function(){
	$('.mCol2_title').attr("data-user",$(this).attr('data-muid'));
	mUserProfile($(this).attr('data-muid'),"click");
	mFetchMsgs($(this).attr('data-muid'),"click");
});
// on click on any user in searched contacts do this
$('#m_contacts_search').on("click",".mC_userLink",function(){
	$('.mCol2_title').attr("data-user",$(this).attr('data-muid'));
	mUserProfile($(this).attr('data-muid'),"click");
	mFetchMsgs($(this).attr('data-muid'),"click");
});
// on send text field (textarea) keypress do this
$('#mSendField').keypress(function (e) {
    if (e.keyCode == 13) {
    	// on [shift + enter] pressed do this
        if (e.shiftKey) {
            return true;
        }
        // on enter button pressed do this
        mSendField($('.mCol2_title').attr('data-user'));
        mRemoveTyping($('.mCol2_title').attr('data-user'));
        this.style.height = '40px';
        $('.mCol2_msgs').css({'bottom':this.style.height});
        return false;
    }
});
// auto hight for send text filed (textarea) code
$('#mSendField').each(function () {
  this.setAttribute('style', 'padding-<? echo lang('float2'); ?>:38px;padding-<? echo lang('float'); ?>:8px;height:40px;overflow-y:hidden;text-align:'+"<?php echo lang('textAlign'); ?>"+';');
}).on('input', function () {
  this.style.height = '40px';
  this.style.height = (this.scrollHeight) + 'px';
  $('.mCol2_msgs').css({'bottom':this.style.height});
});
// on search contacts field [key up] do this
$('#mU_search').keyup(function(){
    mSearchUser();
});
// load contacts on page load
mLoadUsers();
// refresh contacts details every 5 sec
setInterval(mLoadUsers, 5000);
// check if user selected do code in [else] or not do code in first of [if] statement
function getIn2Sec(){
if ($('.mCol2_title').attr('data-user') == "0") {	
}else{
	mUserProfile($('.mCol2_title').attr('data-user'),"timer");
	mFetchMsgs($('.mCol2_title').attr('data-user'),"timer");
}
}
// refresh [getIn2Sec] function ^^^
setInterval(getIn2Sec, 2000);
// typing a message from a user [typing codes]
var lastTypedTime = new Date(0);
function mCheckTyping() {
    if (!$('#mSendField').is(':focus') || $('#mSendField').val() == '' || new Date().getTime() - lastTypedTime.getTime() > 5000) {
        mRemoveTyping($('.mCol2_title').attr('data-user'));
    } else {
        mSetTyping($('.mCol2_title').attr('data-user'));
    }
}
setInterval(mCheckTyping, 100);
$('#mSendField').keypress(function(){lastTypedTime = new Date();});
$('#mSendField').blur(mCheckTyping);
</script>
</body>
</html>