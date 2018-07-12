<?php
while ($postsfetch = $view_posts->fetch(PDO::FETCH_ASSOC)) {
$get_post_id = $postsfetch['post_id'];
$get_author_id = $postsfetch['author_id'];
$get_post_author = $postsfetch['post_author'];
$get_post_author_photo = $postsfetch['post_author_photo'];
$get_post_img = $postsfetch['post_img'];
$get_post_time = $postsfetch['post_time'];
$get_post_content = $postsfetch['post_content'];
$session_userphoto_path = $check_path."imgs/user_imgs/";
$session_userphoto = $session_userphoto_path . $_SESSION['Userphoto'];
$timeago = time_ago($get_post_time);
$get_post_title = $postsfetch['p_title'];
$get_post_privacy = $postsfetch['p_privacy'];
$get_post_shared = $postsfetch['shared'];
switch ($get_post_privacy) {
    case '0':
        $postPrivacy = "<span class='fa fa-globe' data-toggle='tooltip' data-placement='top' title='".lang('wpr_public')."'></span>";
        break;
    case '1':
        $postPrivacy = "<span class='fa fa-users' data-toggle='tooltip' data-placement='top' title='".lang('wpr_followers')."'></span>";
        break;
    case '2':
        $postPrivacy = "<span class='fa fa-lock' data-toggle='tooltip' data-placement='top' title='".lang('wpr_onlyme')."'></span>";
        break;
}
$p_title = $get_post_title;
if (!empty($get_post_shared)) {
    $p_title = lang('shared_a_Post');
}
$qsql = "SELECT * FROM signup WHERE id=:get_author_id";
$query = $conn->prepare($qsql);
$query->bindParam(':get_author_id', $get_author_id, PDO::PARAM_INT);
$query->execute();

while ($query_fetch = $query->fetch(PDO::FETCH_ASSOC)) {
    $query_fetch_id = $query_fetch['id'];
    $query_fetch_username = $query_fetch['Username'];
    $query_fetch_fullname = $query_fetch['Fullname'];
    $query_fetch_userphoto = $query_fetch['Userphoto'];
    $query_fetch_verify = $query_fetch['verify'];
}

$chslq = "SELECT c_id FROM comments WHERE c_post_id=:get_post_id";
$ch = $conn->prepare($chslq);
$ch->bindParam(':get_post_id', $get_post_id, PDO::PARAM_INT);
$ch->execute();
$chtc = $ch->rowCount();
if($chtc == 0){
    $chtcnum = "";
}elseif ($chtc == 1) {
    $chtcnum = "1 ".lang('comment')."";
}else{
    $chtcnum = thousandsCurrencyFormat($chtc)." ".lang('comments')."";
}

$chShare = $conn->prepare("SELECT shared FROM wpost WHERE shared=:get_post_id");
$chShare->bindParam(':get_post_id', $get_post_id, PDO::PARAM_INT);
$chShare->execute();
$chShareCount = $chShare->rowCount();
if($chShareCount == 0){
    $shareCount = "";
}elseif ($chShareCount == 1) {
    $shareCount = "1 ".lang('share')."";
}else{
    $shareCount = thousandsCurrencyFormat($chShareCount)." ".lang('shares')."";
}

$imgs_path = $check_path."imgs/";
$em_img_path = $imgs_path."emoticons/";
if (is_file("config/connect.php")) {
    $includePath = "includes/";
}elseif (is_file("../config/connect.php")) {
    $includePath = "../includes/";
}elseif (is_file("config/connect.php")) {
    $includePath = "../../includes/";
}

include ($includePath."emoticons.php");
$post_body = str_replace($em_char,$em_img,$get_post_content);
$hashtag_path = $check_path."hashtag/";
$hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/';
$body = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $post_body);
if ($isHashTagPage == "yep") {
    $body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2" class="hashtagHightlight">#$2</a>', $body);
}else{
    $body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $body);
}
$body = nl2br("$body");
    echo "
    <div class='post' style='min-width:560px;' id='$get_post_id' style='text-align:".lang('post_align').";'>
    <div id='postNotify_$get_post_id'></div>
    <div class='username_OF_post'>
    <table style='width:100%;'>
    <tr>
    <td style='width:50px;'>
    <div class='username_OF_postImg'><img src=\"".$imgs_path."user_imgs/$query_fetch_userphoto\"></div><td>
    <td>
    <a href='".$check_path."u/$query_fetch_username' class='username_OF_postLink'>$query_fetch_fullname</a><br/>
    <a href='".$check_path."posts/post?pid=".$get_post_id."' class='username_OF_postTime'>$timeago</a>
    </td>
    <td>
    <div class='dropdown'>
    <a class='post_options dropdown-toggle' data-toggle='dropdown' style='float:".lang('post_options').";' href='#'><span>&bull;&bull;&bull;</span></a>
    <ul class='dropdown-menu ".lang('postDropdown')."' style='top:10px;color:#999;text-align: ".lang('postDropdownTxtAlign').";'>
    ";
    if ($get_author_id == $_SESSION['id']) {
            echo "
    <li><a href='javascript:void(0)' onclick=\"editPost('$get_post_id')\"><span class='fa fa-pencil'></span> ".lang('EditPost_DDM')."</a></li>
    <li><a href='javascript:void(0)' onclick=\"deletePost('$get_post_id')\"><span class='fa fa-trash-o'></span> ".lang('DeletePost_DDM')."</a></li>
    <li class='divider'></li>";
        }
    echo "
    <li><a href='javascript:void(0)' onclick=\"savePost('$get_post_id','$check_path')\"><span class='fa fa-bookmark'></span> ".lang('savePost_DDM')."</a></li>
    <li><a href='javascript:void(0)' onclick=\"reportpost('post','$get_post_id')\"><span class='fa fa-bug'></span> ".lang('reportPost_DDM')."</a></li>
    </ul>
    </div>
    </td>
    </tr>
    </table>
    </div><div id='postTitle_$get_post_id'>";
    if (!empty($p_title)) {
        echo "<p class='postTitle' style='border-".lang('float').": 2px solid rgba(80, 94, 113, 0.19); text-align: ".lang('textAlign').";'>$p_title</p>";
    }
    switch ($get_post_privacy) {
        case '0':
            $pub_privacySelected = "selected=''";
            break;
        case '1':
            $f_privacySelected = "selected=''";
            break;
        case '2':
            $om_privacySelected = "selected=''";
            break;
    }
    echo "</div>
    <div class=\"post_content\" style='text-align:".lang('post_content_align').";'>
    <div id='postEditBox_$get_post_id' class='postEditBox' style='display:none;'>
    <input type='text' dir='auto' class='flat_solid_textfield' id='EditTitleBox_$get_post_id' style='min-height: auto;' placeholder='".lang('w_title_inputText')."' value='$p_title' />
    <textarea dir='auto' class='postContent_EditBox' id='EditBox_$get_post_id'>$get_post_content</textarea>
    <div>
    <a href='javascript:void(0)' onclick=\"editPost_save('$get_post_id','$check_path')\" class='default_flat_btn'>".lang('save')."</a>
    <a href='javascript:void(0)' onclick=\"editPost_cancel('$get_post_id')\" class='silver_flat_btn'>".lang('cancel')."</a>
    <select id='p_privacy_$get_post_id' style='padding: 8px 10px;'>
        <option $pub_privacySelected>".lang('wpr_public')."</option>
        <option $f_privacySelected>".lang('wpr_followers')."</option>
        <option $om_privacySelected>".lang('wpr_onlyme')."</option>
    </select>
    </div>
    </div>
    <div id='postLoading_$get_post_id'></div>
    "; 
        echo "<p dir=\"auto\" id='postContent_$get_post_id'>";
    include ("ytframe.php");
    echo "</p>";
    echo""; 
        if (!empty($get_post_img)) {
        echo "<img onclick='lightbox(\"$get_post_id\")' id='lightboxImg_$get_post_id' src=\"".$imgs_path."$get_post_img\" alt='$query_fetch_fullname' />";
        }
// ========= fetch share post ==========
if (!empty(trim($get_post_shared))) {
$fetch_shared = $conn->prepare("SELECT * FROM wpost WHERE post_id=:get_post_shared ");
$fetch_shared->bindParam(':get_post_shared',$get_post_shared,PDO::PARAM_INT);
$fetch_shared->execute();
while ($sharedRow = $fetch_shared->fetch(PDO::FETCH_ASSOC)) {
    $shP_id = $sharedRow['post_id'];
    $shP_aid = $sharedRow['author_id'];
    $shP_img = $sharedRow['post_img'];
    $shP_title = $sharedRow['p_title'];
    $shP_content = $sharedRow['post_content'];
    $shP_time = $sharedRow['post_time'];
    $shP_timeago = time_ago($shP_time);

    $who_shareInfo = $conn->prepare("SELECT * FROM signup WHERE id=:shP_aid ");
    $who_shareInfo->bindParam(':shP_aid',$shP_aid,PDO::PARAM_INT);
    $who_shareInfo->execute();
    while ($user_row = $who_shareInfo->fetch(PDO::FETCH_ASSOC)) {
        $shU_un = $user_row['Username'];
        $shU_up = $user_row['Userphoto'];
        $shU_fn = $user_row['Fullname'];
    }

$sh_post_body = str_replace($em_char,$em_img,$shP_content);
$sh_hashtag_path = $check_path."hashtag/";
$sh_hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$sh_url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/';
$sh_body = preg_replace($sh_url, '<a href="$0" target="_blank" title="$0">$0</a>', $sh_post_body);
if ($isHashTagPage == "yep") {
    $sh_body = preg_replace($sh_hashtags_url, '<a href="'.$sh_hashtag_path.'$2" title="#$2" class="hashtagHightlight">#$2</a>', $sh_body);
}else{
    $sh_body = preg_replace($sh_hashtags_url, '<a href="'.$sh_hashtag_path.'$2" title="#$2">#$2</a>', $sh_body);
}
$sh_body = nl2br("$sh_body");
}
echo "
<div class=\"post\" id=\"$shP_id\" style='margin: 0;text-align:".lang('post_align').";'>
<div class=\"username_OF_post\">
<table style=\"width:100%;\">
<tbody><tr>
<td style=\"width:50px;\">
<div class=\"username_OF_postImg\"><img src=\"".$check_path."imgs/user_imgs/$shU_up\"></div></td><td>
</td><td>
<a href=\"".$check_path."u/$shU_un\" class=\"username_OF_postLink\">$shU_fn</a><br>
<a href=\"".$check_path."posts/post?pid=$shP_id\" class=\"username_OF_postTime\">$shP_timeago</a>
</td>
</tr>
</tbody></table>
</div><div id='postTitle_$get_post_id'>";
if (!empty($shP_title)) {
    echo "<p class='postTitle' style='border-".lang('float').": 2px solid rgba(80, 94, 113, 0.19); text-align: ".lang('textAlign').";'>$shP_title</p>";
}
echo "</div><div class=\"post_content\" style=\"text-align:left;\">
<p dir=\"auto\" id=\"postContent_$shP_id\" style='text-align: ".lang('textAlign').";'>$sh_body";
echo "</p>";
if (!empty($shP_img)) {
echo "<img onclick=\"lightbox('$shP_id')\" id=\"lightboxImg_$shP_id\" src=\"".$check_path."imgs/$shP_img\" alt=\"$shU_fn\">";
}
echo "
</div>
</div>
";
}
// =====================================
echo "
</div>
<div id='postNotify2_$get_post_id'></div>
<div class=\"post_like_comment_share\">
    <a href=\"javascript:void(0);\" onclick=\"fcomment('$get_post_id')\" class='post_like_comment_shareA' data-toggle='tooltip' data-placement='top' title='".lang('comment')."'><span class=\"fa fa-commenting\"></span></a>";
$s_id = $_SESSION['id'];
$csql = "SELECT id FROM likes WHERE liker=:s_id AND post_id=:get_post_id";
$c = $conn->prepare($csql);
$c->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$c->bindParam(':get_post_id',$get_post_id,PDO::PARAM_INT);
$c->execute();
$c_num = $c->rowCount();
if ($c_num > 0){
    echo "<span id='likeUnlike_$get_post_id' style='cursor:pointer'><span onclick=\"likeUnlike('$get_post_id')\" style='color:#ff928a;font-size:30px' data-toggle='tooltip' data-placement='top' title='".lang('u_liked_this')."' id='punlike'><span class=\"fa fa-heart\"></span></span></span>";
}else{
    echo "<span id='likeUnlike_$get_post_id' style='cursor:pointer'><span onclick=\"likeUnlike('$get_post_id')\" style='color:#ff928a;font-size:30px' data-toggle='tooltip' data-placement='top' title='".lang('liked')."' id='plike'><span class=\"fa fa-heart-o\"></span></span></span>";
}
$likes_sql = "SELECT id FROM likes WHERE post_id=:get_post_id";
$likes = $conn->prepare($likes_sql);
$likes->bindParam(':get_post_id',$get_post_id,PDO::PARAM_INT);
$likes->execute();
$likes_num = $likes->rowCount();
if ($likes_num == 0) {
    $likenum = "<span class='fa fa-heart'></span> ".lang('no_likes');
}elseif ($likes_num == 1){
    $likenum = "1 <span class='fa fa-heart' style='color: #ff928a;'></span>";
}else{
    $likenum = thousandsCurrencyFormat($likes_num)." <span class='fa fa-heart' style='color: #ff928a;'></span>";
}

echo "<a href='javascript:void(0);' onclick=\"sharePost('$get_post_id','$check_path')\" class='post_like_comment_shareA' data-toggle='tooltip' data-placement='top' title='".lang('share_now')."'><span class=\"fa fa-share-alt\"></span></a>
    <p class='comment_details' style='text-align:".lang('textAlign').";'>
    <span id='postLikeCount_$get_post_id'>$likenum</span><span style='margin: 0px 5px;padding: 1px;'></span>
    <span id='postCommCount_$get_post_id'>$chtcnum</span><span style='margin: 0px 5px;padding: 1px;'></span>
    <span id='postShareCount_$get_post_id'>$shareCount</span>
    <span id='p_privacyView_$get_post_id' style='top: -20px;float:".lang('float2').";font-size: 15px;'>".$postPrivacy."</span>
    </p>
</div>
<div class=\"comment_box\">
<div class='user_comment' id='postComments_$get_post_id'>";
    include "fetch_comments.php";
echo"
</div>
<div id='writeComm_$get_post_id'>
<div style='position:relative;display:flex;background: #fff; box-shadow: 2px 2px rgba(0, 0, 0, 0.04); border-radius: 20px;'>
  <textarea dir=\"auto\" autocomplete='off' class='comment_field' id='inputComm_$get_post_id' type=\"text\" data-cid='$get_post_id' data-path='$check_path' name=\"$get_post_id\" placeholder='".lang('comment_field_ph')."' style='text-align:".lang('comment_field_align').";' ></textarea>
  <span class='emoticonsBtn fa fa-smile-o' onclick=\"cEmojiBtn('$get_post_id')\" id='#embtn_".$get_post_id."'></span>
      <div id='em_$get_post_id' data-emtog='0' style='".lang('float2').":0;' class='emoticonsBox'></div>
</div>
<p style='font-size: 10px; padding: 0px 10px; border: none; margin: 0; margin-top: 3px;'>".lang('newLine_Shift_enter')." Shift+Enter</p>
</div>
<div id='CommentLoading_$get_post_id'>
</div>
</div></div>
";

}
?>

<script type="text/javascript">
    $('.comment_field').keypress(function (e) {
        var cid = $(this).attr('data-cid');
        var path = $(this).attr('data-path');
        if (e.keyCode == 13) {
            if (e.shiftKey) {
                return true;
            }
            commentodb(cid,path);
            this.style.height = '40px';
            return false;
        }
    });
    $('.comment_field').each(function () {
      this.setAttribute('style', 'height:40px;overflow-y:hidden;text-align:'+"<?php echo lang('textAlign'); ?>"+';');
    }).on('input', function () {
      this.style.height = '40px';
      this.style.height = (this.scrollHeight) + 'px';
    });
</script>