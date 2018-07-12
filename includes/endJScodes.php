<?php
if (is_dir("imgs/")) {
$checkDir = "";
}elseif (is_dir("../imgs/")) {
$checkDir = "../";
}elseif (is_dir("../../imgs/")) {
$checkDir = "../../";
}
?>

<script type="text/javascript">
//==================================================================
function fetchPosts_DB(fetchFrom){
var plimit = $("#GetLimitOfPosts").val();
var rid = "<?php echo $row_id; ?>";
var path = "<?php echo $check_path; ?>";
$('#LoadMorePostsBtn').hide();
$.ajax({
url: "<?php echo $check_path; ?>includes/fetch_posts_"+fetchFrom+".php",
type: "POST",
data: {"path":path, "plimit":plimit, "rid":rid},
beforeSend:function(){
  $('#LoadMorePostsBtn').hide();
  $('#LoadingPostsDiv').show();
},
success:function(data){
if (data == "0") {
$('#LoadMorePostsBtn').hide();
$('#LoadingPostsDiv').hide();
$('#NoMorePostsDiv').show();
}else{
$('#FetchingPostsDiv').append(data);
$('#LoadMorePostsBtn').show();
$('#LoadingPostsDiv').hide();
document.getElementById("GetLimitOfPosts").value = Number(plimit)+10;
}
},
error:function(error){
  alert('error getting posts!');
}
});
}
document.getElementById('GetLimitOfPosts').value = 0;
//==================================================================
function followUnfollow(id){
    $.ajax({
        type:'POST',
        url:"<?php echo $checkDir; ?>includes/f_action.php",
        data:{'id':id},
        beforeSend:function(){
            $('#followUnfollow_'+id).html("<button class=\"unfollow_btn\"><span class=\"fa fa-check\"></span> <?php echo lang('followingBtn_str'); ?></button>");
        },
        success:function(fmsg){
            $('#followUnfollow_'+id).html(fmsg);
        },
        error:function(){
            alert('Some problem occured, please try again.');
        }
    });
}
function likeUnlike(pl){
    $.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/lac.php",
    data:{'pl':pl},
    dataType: "json",
        beforeSend:function(){
            $('#likeUnlike_'+pl).html("<span onclick=\"return false;\" style='cursor: default;color:#ff928a;font-size:30px' data-toggle='tooltip' data-placement='top' title='<?php echo lang('please_wait'); ?>' id='plike'><span class=\"fa fa-heart\"></span></span>");
        },
        success:function(msg){
                $('#likeUnlike_'+pl).html(msg[0]);
                $('#postLikeCount_'+pl).html(msg[1]);
        },
        error:function(){
            alert('Some problem occured, please try again.');
        }
    });
}
function profilePhoto(imgUrl) {
    if (imgUrl.files && imgUrl.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#profilePhotoPreview').attr('src', e.target.result);
            $('#submitProfilePhoto').css({"display":"inline-block"});
        }

        reader.readAsDataURL(imgUrl.files[0]);
    }
}
function profileCoverPhoto(imgUrl) {
    if (imgUrl.files && imgUrl.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#coverImg').css({"background":"url('"+e.target.result+"') no-repeat center center","background-size":"cover"});
            $('#coverBtnCancel').css({"display":"block"});
            $('#coverBtnUp').css({"display":"block"});
        }
        reader.readAsDataURL(imgUrl.files[0]);
    }
}
function editComment(cid){
$('#commentContent_'+cid).hide();
$('#commentEditBox_'+cid).show();
$('#commEditBox_'+cid).focus();
}
function editComment_save(cid,checkPath){
var cContent = $.trim($('#commEditBox_'+cid).val());
var path = "<?php echo $checkDir; ?>";
if (cContent == '') {

}else{
$.ajax({
type: "POST",
url: "<?php echo $checkDir; ?>includes/updatecomment.php",
data: { "cid":cid,"cContent":cContent,"cp":checkPath },
cache: false,
dataType: "json",
beforeSend: function(loading){
$('#commentEditBox_'+cid).hide();
$('#commentContent_'+cid).show();
$('#CommentLoading_'+cid).html("<p style='width: 100%;border:none;'><img src='"+path+"imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> <?php echo lang('posting'); ?> </p>");
$('#commentContent_'+cid).css({'opacity':'0.5'});
},
success: function(res){
$('#editedComment_'+cid).html("<sub style='font-size: 15px; margin: 0px 3px;'>&bull;</sub> <?php echo lang('comm_edited'); ?> ("+res[0]+")");
$('#commentContent_'+cid).html("<span class='spanComment'>"+res[1]+"</span>");
$('#commentContent_'+cid).show();
$('#commentEditBox_'+cid).hide();
$('#commentContent_'+cid).css({'opacity':'1'});
$('#CommentLoading_'+cid).html("");
}
});
}

}
function editComment_cancel(pid){
$('#commentContent_'+pid).show();
$('#commentEditBox_'+pid).hide();
}

function editPost(pid){
$('#postContent_'+pid).hide();
$('#postTitle_'+pid).hide();
$('#p_privacyView_'+pid).hide();
$('#postEditBox_'+pid).show();
$('#EditBox_'+pid).focus();
}
function editPost_save(pid,checkPath){
var pc = $.trim($('#EditBox_'+pid).val());
var pt = $.trim($('#EditTitleBox_'+pid).val());
var pp = $.trim($('#p_privacy_'+pid).val());
var path = "<?php echo $checkDir; ?>";
$.ajax({
type: "POST",
url: "<?php echo $checkDir; ?>includes/updatepost.php",
data: {"pid":pid, "pc":pc, "pt":pt, "pp":pp, "cp":checkPath},
cache: false,
dataType: "json",
beforeSend: function(loading){
$('#postEditBox_'+pid).hide();
$('#postContent_'+pid).show();
$('#postLoading_'+pid).html("<p style='width: 100%;border:none;'><img src='"+path+"imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> <?php echo lang('posting'); ?> </p>");
$('#postContent_'+pid).css({'opacity':'0.5'});
},
success: function(res){
$('#postContent_'+pid).html(res[0]);
$('#postTitle_'+pid).html("<p class='postTitle' style='border-<?php echo lang('float'); ?>: 2px solid #bee1ff; text-align:<?php echo lang('textAlign'); ?>;'>"+res[1]+"</p>");
$('#p_privacyView_'+pid).html(res[2]);
$('#postContent_'+pid).show();
if (res[1].length > 0) {$('#postTitle_'+pid).show();}
$('#p_privacyView_'+pid).show();
$('#postEditBox_'+pid).hide();
$('#postContent_'+pid).css({'opacity':'1'});
$('#postLoading_'+pid).html("");
},
error: function(err){alert(err);}
});

}
function editPost_cancel(pid){
$('#postContent_'+pid).show();
$('#postTitle_'+pid).show();
$('#p_privacyView_'+pid).show();
$('#postEditBox_'+pid).hide();
}
function deletePost(pid){
$.ajax({
    type:'POST',
    url:'<?php echo $checkDir; ?>includes/deletepost.php',
    data:{'pid':pid},
    beforeSend: function(){
    $('#'+pid).hide();
    },
    success: function(done){
        if (done == 'yes') {
            $('#'+pid).html('');
        }else{
            $('#'+pid).show();
            alert('Action denied! You are not allowed to doing this action');
        }
    }
});
}
function commentodb(pid,checkPath){
var cContent = $.trim($('#inputComm_'+pid).val());
var path = "<?php echo $checkDir; ?>";
if(cContent == ''){
}else{
$.ajax({
    type:'POST',
    url:'<?php echo $checkDir; ?>includes/insert_commentdb.php',
    data:{"pid":pid, "cContent":cContent, "cp":checkPath},
    cache: false,
    beforeSend: function(loading){
    $('#writeComm_'+pid).hide();
    $('#CommentLoading_'+pid).html("<p style='text-align:<?php echo lang('textAlign'); ?>;width: 100%;border:none;'><img src='"+path+"imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> <?php echo lang('posting'); ?></p>");
    },
    success: function(comment){
    $('#postComments_'+pid).append(comment).slideDown('slow');
    $('#inputComm_'+pid).val('');
    $('#writeComm_'+pid).show();
    $('#CommentLoading_'+pid).html("");
    }
});
}
}
function deleteComment(cid){
$.ajax({
    type:'POST',
    url:'<?php echo $checkDir; ?>includes/deletecomm.php',
    data:{'cid':cid},
    beforeSend: function(){
        $('#comment_'+cid).hide();
    },
    success: function(done){
        if (done == 'yes') {
            $('#comment_'+cid).html('');
        }else{
            $('#comment_'+cid).show();
            alert('Action denied! You are not allowed to doing this action');
        }
    }
});
}
$('.postContent_EditBox').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;text-align:' + "<?php echo lang('post_textbox_align'); ?>;");
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});
function hideMsg(){
    $('#error_msg').fadeOut('slow');
    $('#success_msg').fadeOut('slow');
}

function savePost(pid,path){
var myid = "<?php echo $_SESSION['id']; ?>";
$.ajax({
    url:"<?php echo $check_path; ?>includes/send_saved_postDB.php",
    type:"POST",
    data:{"pid":pid, "myid":myid, "path":path},
    beforeSend:function(){
    $('#postNotify_'+pid).slideDown(300,function(){
    $('#postNotify_'+pid).html("<p class='postNotify' style='text-align:<?php echo lang('textAlign'); ?>;'><img src='<?php echo $check_path; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> <?php echo lang('please_wait'); ?></p>");
    });
    },
    success:function(data){
    $('#postNotify_'+pid).slideDown(300,function(){
    $('#postNotify_'+pid).html(data);
    });
    },
    error:function(error){
    $('#postNotify_'+pid).slideDown(300,function(){
    $('#postNotify_'+pid).html("<p class='postNotify' style='border-bottom:1px solid red;'><span class='fa fa-times' onclick=\"canselPostNotify('"+pid+"')\"></span> Error, somthing went wrong while saving this post!</p>");
    });
    },
});
}
function canselPostNotify(nid){
    $('#postNotify_'+nid).slideUp(300,function(){$('#postNotify_'+nid).html('');});   
}
function canselPostNotify2(nid){
    $('#postNotify2_'+nid).slideUp(300,function(){$('#postNotify2_'+nid).html('');});   
}
function deleteSavedMsg(sid){
    $("#deleteSavedMsg_"+sid).html("<div style='background: rgba(255, 152, 0, 0.08); padding: 10px; border-radius: 2px;'> <p><b><?php echo lang('do_you_want_to_delete_this'); ?></b> <br/><?php echo lang('you_can_not_undo_this_after_deleting_it'); ?>.</p> <button class='silver_flat_btn' onclick='cancelSavedMsg(\""+sid+"\");'><?php echo lang('cancel'); ?></button> <button class='red_flat_btn' onclick='deleteSaved(\""+sid+"\");'><?php echo lang('delete'); ?></button> </div>");
}
function cancelSavedMsg(sid){
    $("#deleteSavedMsg_"+sid).html("");
}
function deleteSaved(sid){
$.ajax({
    url:"<?php echo $check_path; ?>includes/deleteSavedPost.php",
    type:"POST",
    data:"sid="+sid,
    beforeSend:function(){
        $("#saved_"+sid).hide();
    },
    success:function(){
        $("#saved_"+sid).html("");
    },
    error:function(){
        alert('Error, something went wrong!');
    }
});
}
function starPage(uid,pid){
$.ajax({
    type:"POST",
    url: "<?php echo $check_path; ?>includes/r_star.php",
    data:{'uid':uid,'pid':pid},
    beforeSend:function(){
        $('#rate_star').html("<button class='follow_btn' onclick='starPage(\"$uid\",\"$pid\")' style='width:100%;margin:0px 3px;border-color:#ffc107;padding:10px 15px;' title='<?php echo lang('unstar'); ?>'><span class='fa fa-star' style='color:#FFC107;font-size:18px;'></span></button>");
    },
    success:function(data){
        $('#rate_star').html(data);
    },
    error:function(){
        alert('Error, something went wrong!');
    }
});
}
// ////////// lightboxImg /////////
function lightbox(pid){
    var modal = document.getElementById('lightboxImg_myModal');
    var modalImg = document.getElementById("lightboxImg_photo");
    var img = document.getElementById('lightboxImg_'+pid);
    modal.style.display = "block";
    modalImg.src = img.src;
    $('body').css({'overflow':'hidden'});
}
function lightboxClose(){
    var modal = document.getElementById('lightboxImg_myModal');
    modal.style.display = "none";
    $('body').css({'overflow':'auto'});
}
///////////////////////////////////
function cEmoji(cid,em){
    var append = $('#inputComm_'+cid).val()+em+" ";
    $('#inputComm_'+cid).val(append);
    $('#inputComm_'+cid).focus();
}
function cEmojiBtn(cid){
var checkData = $('#em_'+cid).html();
if($('#em_'+cid).attr('data-emtog') == '0'){
    $('#em_'+cid).show();
    $('#em_'+cid).attr('data-emtog','1');
    if(checkData.length == 0){
        var path = "<?php echo $check_path; ?>";
        var emType = "comment";
        $.ajax({
        type:"POST",
        url: "<?php echo $check_path; ?>includes/emoticons_c_m.php",
        data:{'path':path,'cid':cid,'emType':emType},
        beforeSend:function(){
        $('#em_'+cid).show();
        $('#em_'+cid).html("<p align='center' style='margin:0;width: 100%;border:none;'><img src='"+path+"imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>");
        },
        success:function(data){
        $('#em_'+cid).html("<div'><span class='toTopArrow' style='position: absolute; top: -10px;"+"<?php echo lang('float2'); ?>"+":8px;'></span></div>"+data);
        },
        error:function(){
            alert('Error, something went wrong!');
        }
        });
    }
}else{
    $('#em_'+cid).hide();
    $('#em_'+cid).attr('data-emtog','0');
}
}
////////////////////////////////////
function mEmoji(em){
    var append = $('#mSendField').val()+em+" ";
    $('#mSendField').val(append);
    $('#mSendField').focus();
}
function mEmojiBtn(){
var checkData = $('#emBox').html();
if($('#emBox').attr('data-emtog') == '0'){
    $('#emBox').show();
    $('#emBox').attr('data-emtog','1');
    if(checkData.length == 0){
        var path = "<?php echo $checkDir; ?>";
        var emType = "message";
        $.ajax({
        type:"POST",
        url: "<?php echo $checkDir; ?>includes/emoticons_c_m.php",
        data:{'path':path,'emType':emType},
        beforeSend:function(){
        $('#emBox').show();
        $('#emBox').html("<p align='center' style='margin:0;width: 100%;border:none;'><img src='"+path+"imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>");
        },
        success:function(data){
        $('#emBox').html("<div><span class='toBottomArrow' style='position: absolute; bottom: -10px;"+"<?php echo lang('float2'); ?>"+":8px;'></span></div>"+data);
        },
        error:function(){
            alert('Error, something went wrong!');
        }
        });
    }
}else{
    $('#emBox').hide();
    $('#emBox').attr('data-emtog','0');
}
}
 $('#sendField').keypress(function(){
    $('#emBox').hide();
    $('#emBox').attr('data-emtog','0');
 });
////////////////////////////////////
function fcomment(txtfid){
    $('#inputComm_'+txtfid).focus();
}
function sharePost(pid,path){
$.ajax({
    url:"<?php echo $check_path; ?>includes/share.php",
    type:"POST",
    data:{"pid":pid, "path":path},
    beforeSend:function(){
    $('#postNotify2_'+pid).slideDown(300,function(){
    $('#postNotify2_'+pid).html("<p class='postNotify' style='text-align:<?php echo lang('textAlign'); ?>;'><img src='<?php echo $check_path; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> <?php echo lang('please_wait'); ?></p>");
    });
    },
    success:function(data){
    $('#postNotify2_'+pid).slideDown(300,function(){
    $('#postNotify2_'+pid).html(data);
    });
    },
    error:function(error){
    $('#postNotify2_'+pid).slideDown(300,function(){
    $('#postNotify2_'+pid).html("<p class='postNotify' style='border-bottom:1px solid red;'><span class='fa fa-times' onclick=\"canselPostNotify2('"+pid+"')\"></span> <?php echo lang('errorSomthingWrong'); ?></p>");
    });
    },
});
}
function reportpost(type,fid){
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/report.php",
    data:{'type':type,'fid':fid},
    beforeSend:function(){
    $('#postNotify_'+fid).slideDown(300,function(){
    $('#postNotify_'+fid).html("<p class='postNotify'><img src='<?php echo $check_path; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> Sending your report...</p>");
    });
    },
    success:function(data){
    $('#postNotify_'+fid).slideDown(300,function(){
    $('#postNotify_'+fid).html(data);
    });
    }
});
}
function submitreport(){
    var type = "problem";
    var sub = $('#report_sub').val();
    var sub = sub.trim();
    var txt = $('#report_txt').val();
    var txt = txt.trim();
if (sub != "" && txt != "") {
$.ajax({
type:'POST',
url:"<?php echo $checkDir; ?>includes/report.php",
data:{'type':type,'sub':sub,'txt':txt},
beforeSend:function(){
$('#report_submit').hide();
$('#SubLog').html("<p><img src='<?php echo $checkDir; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'> Sending your report...</p>");
},
success:function(data){
if (data == "done") {
    window.location.href = "<?php echo $checkDir; ?>page/supportbox";
}else{
    $('#report_submit').show();
    $('#SubLog').html("<p style='color:red;'>"+data+"</p>");
}
}
});
}else{
    $('#SubLog').html("<p style='color:red;'>Fill required fields</p>");
}
}
function deleteReport(rid){
    var type = "deleteReport";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/report.php",
    data:{'type':type,'rid':rid},
    beforeSend:function(){
    $('#delR_'+rid).html("<p style='margin:0;'><img src='<?php echo $checkDir; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>");
    },
    success:function(data){
    if (data == "done") {
        $('#report_'+rid).html('');
        $('#report_'+rid).hide();
    }else{
        alert('Error, please try again later!');
    }
    }
});
}
// messages requsets like search or fetch users and send .....
function mLoadUsers(){
mLoadUsers2();
var requ = "getUsers";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path},
    success:function(data){
     if (data =='') {
        $('#m_contacts_friends').html("<p style='margin: 0px 8px; text-align: center; color: grey; background: rgba(0, 0, 0, 0.03); padding: 8px 0px; border-radius: 3px;'><? echo lang('nothingToShow'); ?></p>");
     }else{
     $('#m_contacts_friends').html(data);
     }
    }
});
}
function mLoadUsers2(){
var requ = "getUsers2";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path},
    success:function(data){
     if (data =='') {
        $('#m_contacts_requests').html("<p style='margin: 0px 8px; text-align: center; color: grey; background: rgba(0, 0, 0, 0.03); padding: 8px 0px; border-radius: 3px;'><? echo lang('nothingToShow'); ?></p>");
     }else{
     $('#m_contacts_requests').html(data);        
     }
    }
});
}
function mSearchUser(){
var requ = "searchUser";
var path = "<?php echo $checkDir; ?>";
var mSearch = $.trim($('#mU_search').val());
if (mSearch != '') {
$('#m_contacts').hide();
$('#m_contacts_search').show();
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'mSearch':mSearch},
    beforeSend:function(){
        $('#m_contacts_search').html("<div style='text-align: center; padding: 15px;'><img src='"+path+"imgs/loading_video.gif'></div>");
    },
    success:function(data){
     $('#m_contacts_search').html(data);
    }
});
}else{
   mLoadUsers();
   $('#m_contacts').show();
   $('#m_contacts_search').hide();
}
}

function mUserProfile(uid,type){
var requ = "userProfile";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    dataType: "json",
    beforeSend:function(){
        if (type == "click") {
        $('.mCol3_userInfo').html(`
        <div style="position: relative;">
        <div class="mCol3_userInfo_avatar">
        </div>
        <div class="mCol3_userActive" style="background: #ccc;<? echo lang('float2'); ?>:55%;"></div>
        </div>
        <h4 style="text-align: center;"><div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></h4>
        <p style="text-align:center;margin: 0px;color: gray"><div style="width: 40%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></p>
            `);
        $('.mCol3_bio').html(`
        <div style="width: 80%; height: 10px; background: rgba(217, 221, 224, 0.55);"></div>
        <div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55);margin-top: 8px;"></div>
        `);
        }
    },
    success:function(data){
        $('.mCol3_userInfo').html(data[0]);
        $('.mCol3_bio').html(data[1]);
        $('.mCol2_title').html(data[2]);
    }
});
}
function mFetchMsgs(uid,type){
var requ = "fetchMsgs";
var path = "<?php echo $checkDir; ?>";
var mCountToScroll = $('.m_msgTable').attr('data-count');
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    beforeSend:function(){
    if (type == "click") {
        $('#m_messages_loading').show();
        $('.selectToChat').hide();
    }else{}
    },
    success:function(data){
    mTypingStatus(uid);
    $('#m_messages_loading').hide();
    $('#m_messages').html(data);
    mCheckSeen(uid);
    if ($('.m_msgTable').attr('data-count') != mCountToScroll) {
    $('.mCol2_msgs').animate({ scrollTop:$('.mCol2_msgs').prop('scrollHeight')}, 0);
    }
    }
}); 
}
function mCheckSeen(uid){
var requ = "checkSeen";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    success:function(data){
    if (data > 0) {
        $('#m_userSeen').show();

    }else{
        $('#m_userSeen').hide();
    }
    }
}); 
}
function mSendField(uid){
var requ = "sendMsg";
var path = "<?php echo $checkDir; ?>";
var msg = $.trim($('#mSendField').val());
if (msg != '' && uid != 0) {
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid,'msg':msg},
    success:function(data){
    if (data == "error") {
        alert('error');
    }else{
        $('#mSendField').val('');
        $('#mSendField').focus();
    }
    }
});
}else{
    
}
}
// typing [ststus] message from user
function mTypingStatus(uid){
var requ = "checkTyping";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    success:function(data){
    if (data > 0) {
        $('.mCol2_msgs').animate({ scrollTop:$('.mCol2_msgs').prop('scrollHeight')}, 0);
        $('#m_userTyping').show();
    }else{
        $('#m_userTyping').hide();
    }
    }
}); 
}
function mSetTyping(uid){
var requ = "mTyping";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    success:function(data){
    }
});
}
function mRemoveTyping(uid){
var requ = "mUnTyping";
var path = "<?php echo $checkDir; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $checkDir; ?>includes/m_requests.php",
    data:{'req':requ,'path':path,'uid':uid},
    success:function(data){
    }
});
}
// ===========================================================

</script>
