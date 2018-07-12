<style type="text/css">
    #cancel_photo_preview{
    display: block;
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 21px;
    transition: color 0.3s;
    text-shadow: 1px 1px 8px rgb(0, 0, 0);
    }
    #photo_preview label{
    position: absolute;
    margin: 0;
    padding: 0;
    background: rgba(0, 0, 0, 0.46);
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    }
</style>
<?php
if (is_dir("imgs/")) {
    $imagePath = "imgs/";
}elseif (is_dir("../imgs/")) {
    $imagePath = "../imgs/";
}elseif (is_dir("../../imgs/")) {
    $imagePath = "../../imgs/";
}
?>
<div class="post" style="text-align:<?php echo lang('w_post_align'); ?>;direction: <?php echo lang('w_post_dir'); ?>">
<table class="WritePostUserI">
<tr>
<td style='width:50px;'>
<div class='username_OF_postImg'><img src="<?php echo $imagePath; ?>user_imgs/<?php echo $_SESSION['Userphoto']; ?>"></div>
</td>
<td style="padding: 10px 0px">
<a href="<?php echo $check_path; ?>u/<?php echo $_SESSION['Username']; ?>"><?php echo $_SESSION['Fullname']; ?></a><br/>
<span class='username_OF_postTime'>@<?php echo $_SESSION['Username']; ?></span>
</td>
</table>
<form id="postingToDB" action="<?php echo $check_path; ?>includes/wpost.php" method="post" enctype="multipart/form-data" style="margin: 0;">
<div id="w_text" class="wpost_tabcontent" style="display:block;padding: 0px">
    <textarea dir="auto" id="lang_rtl_ltr" class="post_textbox" placeholder="<?php echo lang('post_textbox_placeholder'); ?>" name="post_textbox" ></textarea>
</div>
<div id="w_photo" class="wpost_tabcontent">
   <label>
        <input type="file" name="w_photo" accept="image/png, image/jpeg, image/jpeg" onchange="photoPreview(this);" style="display: none" />
       <div id='photo_preview' style="margin-top: 10px;order: 1px solid rgba(0, 0, 0, 0.1);overflow: hidden;width: 100px;height: 100px;display:none;position: relative;">
       <label style="color: white">
        <button type="reset" name="reset" id="cancel_photo_preview" style="display: none"><span class="fa fa-times"></span></button>
        
       </label>
            <img id='photo_preview_src' src='#' alt='your image' style='height:100%;cursor: pointer;' />
       </div>

       
    <div id='photo_preview_box' style='cursor: pointer;display:block;width:100px;height:100px;border:2px dashed rgba(78, 178, 255, 0.8);text-align:center;'>
        <span class='fa fa-image' style='    margin: 35%;font-size: 30px;color: rgba(78, 178, 255, 0.8);'></span>
         </div>
   </label>
</div>
<div id="w_title" class="wpost_tabcontent">
<input type="text" name="w_title" maxlength="100" id="your_title" placeholder="<?php echo lang('w_title_inputText'); ?>" class="flat_solid_textfield"></input>
<input type="hidden" name="check_path" value="<?php echo $check_path; ?>" />
/ 100
</div>
<div>
<ul class="wpost_tab">
    <li id="wt_text" style="float:<?php echo lang('w_post_li'); ?>;">
    <button class="wpost_tablinks" onclick="wpost_tabs(event, 'w_text')"><span style="color: cornflowerblue;margin: 0px 5px;" class="fa fa-pencil"></span> <?php echo lang('wpost_write'); ?></button>
    </li>
    <li id="wt_photo" style="float:<?php echo lang('w_post_li'); ?>;">
    <button class="wpost_tablinks" onclick="wpost_tabs(event, 'w_photo')"><span style="color: #4CAF50;margin: 0px 5px;" class="fa fa-camera"></span> <?php echo lang('wpost_upPhoto'); ?></button>
    </li>
    <li id="wt_location" style="float:<?php echo lang('w_post_li'); ?>;">
    <button class="wpost_tablinks" onclick="wpost_tabs(event, 'w_title')"><span style="color: #ffb300;margin: 0px 5px;" class="fa fa-quote-right"></span> <?php echo lang('wpost_title'); ?></button>
    </li>
    <li style="float:<?php echo lang('w_post_li2'); ?>;">
    <input class="default_flat_btn" type="submit" name="post_now" value="<?php echo lang('post_now'); ?>" style="margin: 5px;padding: 8px 10px;" />
    </li>
    <li style="float:<?php echo lang('w_post_li2'); ?>;">
        <select id="p_privacy" style="margin: 5px; padding: 0px 10px; max-width: 110px; height: 35px;" name="w_privacy">
            <option selected=""><?php echo lang('wpr_public'); ?></option>
            <option><?php echo lang('wpr_followers'); ?></option>
            <option></span> <?php echo lang('wpr_onlyme'); ?></option>
        </select>
    </li>
</ul>
</div>
   </form>
<div class="loadingPosting"><p class="loadingPostingP">0</p></div>
</div>
<div id="getingNP"></div>
<script>
$(document).ready(function(){
$('.loadingPosting').hide();
var i = 1;
$("#postingToDB").on('submit',function(e){
if ($.trim($('.post_textbox').val()) != "") {
var plus = i++;
$("#getingNP").prepend("<div id='FetchingNewPostsDiv"+plus+"' style='display:none;'></div>");
e.preventDefault();   
$(this).ajaxSubmit({
beforeSend:function(){
$("#postingToDB").slideUp();
$('.loadingPosting').show();
$(".loadingPostingP").css({'width' : '0%'});
$(".loadingPostingP").html('0');
},
uploadProgress:function(event,position,total,percentCompelete){
$(".loadingPostingP").css({'width' : percentCompelete + '%'});
$(".loadingPostingP").html(percentCompelete);
},
success:function(data){
$("#postingToDB").slideDown(function(){
    $('.loadingPosting').slideUp(function(){
        $("#FetchingNewPostsDiv"+plus).html(data);
        $("#FetchingNewPostsDiv"+plus).fadeIn();
    });
    $('.post_textbox').css({'height':'95px'});
    $("#postingToDB").clearForm();
    $('#w_photo').hide();
    $('#w_title').hide();
    $('#p_privacy').val("<?php echo lang('wpr_public'); ?>");
    $('#photo_preview').hide();
    $('#cancel_photo_preview').hide();
    $('#photo_preview_box').show();
});
}
});
}else{
    return false;
}
});
});
</script>

<script type="text/javascript">
function wpost_tabs(e,tabName){
    e.preventDefault();
switch(tabName){
    case "w_text":
        $('#w_text').show();
        $('.post_textbox').focus();
        $('#w_photo').slideUp();
        $('#w_title').slideUp();
    break;
    case "w_photo":
        $('#w_photo').slideToggle(300);
    break;
    case "w_title":
        $('#w_title').slideToggle(300);
    break;
}
}

$('#your_title').maxlength();
function photoPreview(input) {
if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
    $('#photo_preview_src').attr('src', e.target.result);
    $('#photo_preview_box').css({"display":"none"});
    $('#photo_preview').css({"display":"block"});
    $('#cancel_photo_preview').css({"display":"block"});
    }

    reader.readAsDataURL(input.files[0]);
}else{
    $('#photo_preview_box').css({"display":"block"});
    $('#photo_preview').css({"display":"none"});
    $('#cancel_photo_preview').css({"display":"none"});
}
}
$(document).ready(function(){
    $('#cancel_photo_preview').hide();
    $('#cancel_photo_preview').click(function(){
    $('#photo_preview').hide();
    $('#cancel_photo_preview').hide();
    $('#photo_preview_box').show();
    });
});
$('.post_textbox').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;text-align:' + "<?php echo lang('post_textbox_align'); ?>;");
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});
</script>