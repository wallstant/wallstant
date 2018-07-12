<?php
$pid = $get_post_id;
$show_comments_sql = "SELECT * FROM comments WHERE c_post_id=:pid ORDER BY c_time";
$show_comments = $conn->prepare($show_comments_sql);
$show_comments->bindParam(':pid',$pid,PDO::PARAM_INT);
$show_comments->execute();

while ($comments_fetch = $show_comments->fetch(PDO::FETCH_ASSOC)) {
    $id_4comm = $comments_fetch['c_id'];
    $author_id_4comm = $comments_fetch['c_author_id'];
    $post_id_4comm = $comments_fetch['c_post_id'];
    $content_4comm = $comments_fetch['c_content'];
    $edited_4comm = $comments_fetch['c_edited'];
    $timeEdited_4comm = $comments_fetch['c_time_edited'];
    $timeEdited_4commAgo = time_ago($timeEdited_4comm);
    $time_4comm = $comments_fetch['c_time'];
    $comment_time = time_ago($time_4comm);

    $query2_sql = "SELECT * FROM signup WHERE id=:author_id_4comm";
    $query2 = $conn->prepare($query2_sql);
    $query2->bindParam(':author_id_4comm',$author_id_4comm,PDO::PARAM_INT);
    $query2->execute();
    while ($query_fetch2 = $query2->fetch(PDO::FETCH_ASSOC)) {
        $query_fetch_id2 = $query_fetch2['id'];
        $query_fetch_username2 = $query_fetch2['Username'];
        $query_fetch_fullname2 = $query_fetch2['Fullname'];
        $query_fetch_userphoto2 = $query_fetch2['Userphoto'];
        $query_fetch_verify2 = $query_fetch2['verify'];
    }
    if ($query_fetch_verify2 == "1"){
        $verifypage_var = $verifyUser;
        }else{
        $verifypage_var = "";
    }
    $uProfilePic_path = $check_path."imgs/user_imgs/$query_fetch_userphoto2";
    $uProfileUrl = $check_path."u/$query_fetch_username2";
    $em_img_path = $check_path."imgs/emoticons/";
    $comm_body = str_replace($em_char,$em_img,$content_4comm);
    $hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
    $comm_body = preg_replace($hashtags_url, "<a href='".$check_path."hashtag/$2' title='#$2'>#$2</a>", $comm_body);
    $comm_body = nl2br($comm_body);
    echo "
    <table style='width:100%;' id='comment_$id_4comm' class='uComment'>
    <tr><td style='width:50px;position:relative'>
    <div class='user_comment_img'>
     <img src='$uProfilePic_path'/>
    </div>
    </td><td><a class='userLinkComment' href='$uProfileUrl'>$query_fetch_fullname2</a><span>$verifypage_var </span>
    <p style='word-break: break-word;' id='commentContent_$id_4comm'>
    <span class='spanComment'>$comm_body</span><br/>
    <p style='margin: 0; padding: 0;'>
    <span class='comment_time'>$comment_time</span>";
     if ($edited_4comm == "1") {
        $editedComment = " <sub style='font-size: 15px; margin: 0px 3px;'>&bull;</sub> ".lang('comm_edited')." ($timeEdited_4commAgo)";
     }else{
        $editedComment="";
     }
     echo"<span id='editedComment_$id_4comm' style='font-size:11px;color:#808080;'> $editedComment</span>
    </p>
    </p>
    <div id='CommentLoading_$id_4comm'>
    </div>
    <div id='commentEditBox_$id_4comm' style='display:none;'>
    <textarea dir='auto' class='commentContent_EditBox' id='commEditBox_$id_4comm'>$content_4comm</textarea>
    <div style='margin-bottom: 15px;margin-top: 5px;'>
    <a href='javascript:void(0)' onclick=\"editComment_save('$id_4comm','$check_path')\" class='default_flat_btn'>".lang('save')."</a>
    <a href='javascript:void(0)' onclick=\"editComment_cancel('$id_4comm')\" class='silver_flat_btn'>".lang('cancel')."</a>
    </div>
    </td>
    <td>
    <div class='dropdown'>
    <a class='post_options dropdown-toggle' data-toggle='dropdown' style='float:".lang('float2').";' href='#'><span>&bull;&bull;&bull;</span></a>
    <ul class='dropdown-menu ".lang('postDropdown')."' style='top:10px;color:#999;text-align: ".lang('textAlign').";'>
    ";
    if ($author_id_4comm == $_SESSION['id']) {
    echo " 
    <li><a href='javascript:void(0)' onclick=\"editComment('$id_4comm')\"><span class='fa fa-pencil-square-o'></span> ".lang('comm_edit')."</a></li>
    <li><a href='javascript:void(0)' onclick=\"deleteComment('$id_4comm')\"><span class='fa fa-trash-o'></span> ".lang('comm_delete')."</a></li>";
    }else{
    echo "
    <li><a href='javascript:void(0)' onclick=\"return false;\"><span class='fa fa-bug'></span> ".lang('comm_report')."</a></li>";
    }
    echo"
    </ul>
    </div>
    </td>
    </tr>
</table>
        ";

}
?>


