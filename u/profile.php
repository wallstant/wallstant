<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$myId = $_SESSION['id'];
include("../config/connect.php");
include("../includes/fetch_users_info.php");
include("../includes/time_function.php");
include("../includes/country_name_function.php");
include("../includes/num_k_m_count.php");
if(!isset($_SESSION['Username'])){
    header("location: ../index");
}

$_SESSION['user_photo'] = $row_author_photo;
if (is_dir("imgs/")) {
        $check_path = "";
    }elseif (is_dir("../imgs/")) {
        $check_path = "../";
    }elseif (is_dir("../../imgs/")) {
        $check_path = "../../";
    }
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><?php echo $row_username; ?> | Wallstant</title>
    <meta charset="UTF-8">
    <meta name="description" content="<?php echo $row_bio; ?>">
    <meta name="keywords" content="social network,social media,Wallstant,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "../includes/head_imports_main.php";?>
    </script>
    <style type="text/css">
        .user_info{
        text-align:<?php echo lang('user_info_align');?>;
        }
        .comment_field{
        text-align:<?php echo lang('comment_field_align');?>;
        }
        .coveruploadBtn{
        cursor: pointer;
        background: rgba(74, 74, 74, 0.33);
        padding: 10px 20px;
        border-radius: 3px;
        color: #fff;
        text-decoration: none;
        border: none;
        margin: auto;
        box-shadow: 0;
        -webkit-transition: background 0.3s,box-shadow 0.3s;
        -moz-transition: background 0.3s,box-shadow 0.3s;
        -o-transition: background 0.3s,box-shadow 0.3s;
        transition: background 0.3s,box-shadow 0.3s;
        }
        .coveruploadBtn:hover,.coveruploadBtn:focus{
        background: rgba(0, 0, 0, 0.75);
        box-shadow: 0px 0px 3px #4c4a4a;
        color: #fff;
        text-decoration: none;
        }
    </style>
</head>
    <body onload="fetchPosts_DB('user')">
<!--=============================[ NavBar ]========================================-->
<?php include "../includes/navbar_main.php"; ?>

<?php
if (filter_var(htmlspecialchars($_GET['u']),FILTER_SANITIZE_STRING) == $row_username) {
?>
<!--=============================[ Container ]=====================================-->
        <div class="profile_sec1_sec2" align="center">
        <div class="profile_cover">
                    <?php
                    include "../includes/uploadcoverphoto.php";
                    ?>
        <div id="coverImg" style="height: 100%; width: 100%; background: url(../imgs/user_covers/<?php echo $row_user_cover_photo; ?>) no-repeat center center; background-size: cover;">
            <?php
            if ($row_username == $_SESSION['Username']) {
            ?>
            <div class="profile_coverActions">
                    <form action="" method="post" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td>
                            <label class="coveruploadBtn p_c_upload">
                            <span class="fa fa-camera"></span> <?php echo lang('uploadPhoto'); ?>
                            <input type="file" style="display: none;" name="coveruploadfield" onchange="profileCoverPhoto(this)" />
                            </label>
                            </td>
                            <td>
                            <button style="margin: 0px 5px;display: none;" type="submit" name="coveruploadsubmit" id="coverBtnUp" class="green_flat_btn p_c_save"><span class="fa fa-check"></span> <?php echo lang('save'); ?></button>
                            </td>
                            <td>
                            <button style="display: none;" class="red_flat_btn p_c_cancel" id="coverBtnCancel"><span class="fa fa-times"></span> <?php echo lang('cancel'); ?></button>
                            </td>
                        </tr>
                    </table>
                    </form>
            </div>
            <?php
            }
            ?>
        </div>
        </div>
        <div class="profile_menu">
            <div class="profile_menu_details" style="display:inline-flex;">
                <?php
                $posts_num_sql = "SELECT post_id FROM wpost WHERE author_id=:row_id";
                $posts_num = $conn->prepare($posts_num_sql);
                $posts_num->bindParam(':row_id',$row_id,PDO::PARAM_INT);
                $posts_num->execute();
                $posts_num_int = $posts_num->rowCount();
                //=====================================================================
                $stars_num_sql = "SELECT id FROM r_star WHERE p_id=:row_id";
                $stars_num = $conn->prepare($stars_num_sql);
                $stars_num->bindParam(':row_id',$row_id,PDO::PARAM_INT);
                $stars_num->execute();
                $stars_num_int = $stars_num->rowCount();
                //=====================================================================
                $followers_sql = "SELECT id FROM follow WHERE uf_two=:row_id";
                $followers = $conn->prepare($followers_sql);
                $followers->bindParam(':row_id',$row_id,PDO::PARAM_INT);
                $followers->execute();
                $followers_num = $followers->rowCount();
                //=====================================================================
                $following_sql = "SELECT id FROM follow WHERE uf_one=:row_id";
                $following = $conn->prepare($following_sql);
                $following->bindParam(':row_id',$row_id,PDO::PARAM_INT);
                $following->execute();
                $following_num = $following->rowCount();
                ?>
                <a href='<?php echo $row_username;?>&ut=posts' id='posts_btn' class='profileMenuItem'>
                <b><?php echo thousandsCurrencyFormat($posts_num_int); ?></b> <?php echo lang('posts_str'); ?></a>
                <a href='<?php echo $row_username;?>&ut=stars' id='posts_btn' class='profileMenuItem'>
                <b><?php echo thousandsCurrencyFormat($stars_num_int); ?></b> <?php echo lang('stars_str'); ?></a>
                <span id='followersCount' style="display:inline-flex;">
                <a href='<?php echo $row_username;?>&ut=followers' id='followers_btn' class='profileMenuItem'>
                <b><?php echo thousandsCurrencyFormat($followers_num); ?></b> <?php echo lang('followers_str'); ?></a></span>
                <a href='<?php echo $row_username;?>&ut=following' id='following_btn' class='profileMenuItem'>
                <b><?php echo thousandsCurrencyFormat($following_num); ?></b> <?php echo lang('following_str'); ?></a>
            </div>
        </div>
            <div align="center" style="display: inline-flex;margin: 0px 10px;">
        <div  class="profile_cneterCol">
        <div class="user_info" style="overflow: visible;position: relative;">
            <?
            if ($row_username == $_SESSION['Username']) {
                $userActive = "#4CAF50";
            }else{
                if ($row_online == "1") {
                    $userActive = "#4CAF50";
                }else{
                    $userActive = "#ccc";
                }
            }
            ?>
            <div class="userActive" style="background:<? echo $userActive.';'.lang('float2'); ?>:100px;"></div>
        <?php
         if ($row_profile_pic_border == "1") {
             $profile_pic_border_var = "border-radius: 5%";
         }else{
             $profile_pic_border_var = "";
         }
        ?>
        <div class="profile_picture_img profile_ppicture" style="<?php echo $profile_pic_border_var;?>">
            <img src="<?php echo "../imgs/user_imgs/$row_user_photo";?>" alt="<?php echo $row_fullname;?>" id="profilePhotoPreview" />
            <?php
            include "../includes/uploadprofilephoto.php";
            if($_SESSION['Username'] == $row_username){
                echo "
                <div class=\"change_user_photo\">

                <form action=\"\" method=\"post\" enctype=\"multipart/form-data\">
                <label style='margin:0;'>
                    <p style='margin:0;color: #fff;text-align: center;'><span class=\"fa fa-camera\"></span> ".lang('uploadPhoto')."</p>
                    <input style=\"display: none;\" type=\"file\" accept=\"image/png, image/jpeg, image/jpeg\" name=\"photo_field\" onchange='profilePhoto(this);' />
                </label>
                <button type=\"submit\" name=\"submit_photo\" id='submitProfilePhoto' style='display:none;' >
                <span class='fa fa-check' style='
                background:rgba(62, 187, 74, 0.88);width: 100%;border-radius: 3px;padding: 2px;color: #fff;'> <span style='font-family: sans-serif;'>".lang('save')."
                <span></span></span></span></button>
                </form>
                </div>
                ";
            }
            ?>
            </div>
            <?php
            $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/';
            $website_row = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $row_website);
            ?>
        <div class="profile_picture">
            <h3 style="margin-top:30px; "><?php echo "<a href='".$row_username."'>$row_fullname</a>"; if ($row_verify == "1"){echo $verifyUser;} ?></h3>
            <p align="center">@<?php echo $row_username;?></p>
            <div style="display: flex;">
            <?php
               if($row_id != $_SESSION['id']){
                $csql = "SELECT id FROM follow WHERE uf_one=:s_id AND uf_two=:row_id";
                $c = $conn->prepare($csql);
                $c->bindParam(':s_id',$s_id,PDO::PARAM_INT);
                $c->bindParam(':row_id',$row_id,PDO::PARAM_INT);
                $c->execute();
                $c_num = $c->rowCount();
                if ($c_num > 0){
                    echo "<span id='followUnfollow_$row_id' style='cursor:pointer;width:100%;display:inline-flex;'><button class=\"unfollow_btn\" onclick=\"followUnfollow('$row_id')\"><span class=\"fa fa-check\"></span> ".lang('followingBtn_str')."</button></span>";
                }else{
                    echo "<span id='followUnfollow_$row_id' style='cursor:pointer;width:100%;display:inline-flex;'><button class=\"follow_btn\" onclick=\"followUnfollow('$row_id')\"><span class=\"fa fa-plus-circle\"></span> ".lang('followBtn_str')."</button></span>";
                }
                $sql = "SELECT id FROM r_star WHERE u_id = :uid AND p_id =:pid";
                $starCheck = $conn->prepare($sql);
                $starCheck->bindParam(':uid',$myId,PDO::PARAM_INT);
                $starCheck->bindParam(':pid',$row_id,PDO::PARAM_INT);
                $starCheck->execute();
                $starCheckExist = $starCheck->rowCount();
                if ($starCheckExist > 0) {
                echo "<span id='rate_star'><button class='follow_btn' onclick='starPage(\"$myId\",\"$row_id\")' style='width:100%;margin:0px 3px;border-color:#ffc107;padding:10px 15px;' title='".lang('unFavoritePage')."'><span class='fa fa-star' style='color:#FFC107;font-size:18px;'></span></button></span>";
                
                }else{
                echo "<span id='rate_star'><button class='follow_btn' onclick='starPage(\"$myId\",\"$row_id\")' style='width:100%;margin:0px 3px;padding:10px 15px;' title='".lang('addToFavoritePages')."'><span class='fa fa-star-o' style='color:#bbbbbb;font-size:18px;'></span></button></span>";
                }
                }
                ?>
                </div>
                <?php
                if($row_id == $_SESSION['id']){
                    echo "<span style='cursor:pointer;width:100%;display:inline-flex;'><a href='../settings?tc=edit_profile' class=\"silver_flat_btn\" style='width:100%;'><span class=\"fa fa-cog\"></span> ".lang('edit_profile')."</a></span>";
                }
                ?>
        </div>
      </div>
            <div class="user_info">
            <p style="font-size: 18px;"><?php echo lang('about');?>
            <?php
            $firstname = $row_fullname;
            echo strtok($firstname, " ");
            ?>
            </p><hr style="margin: 0;margin-bottom: 10px;">
            <table>
            <?php
            if(empty($row_school) && empty($row_work0) && empty($row_work) && empty($row_country) && empty($row_birthday) && empty($row_website)){echo "<p>".lang('nothingToShow')."</p>";}
            if (!empty($row_school)){echo "<tr><td class='user_info_tdi'><i class=\"fa fa-graduation-cap\"></i></td><td>".lang('studies')." $row_school<td></tr>";}
            if (!empty($row_work0) || !empty($row_work)){echo "<tr><td class='user_info_tdi'><i class=\"fa fa-briefcase\"></i></td><td>".lang('working')." $row_work0 ".lang('at')." $row_work<td></tr>";}
            if (!empty($row_country)){echo "<tr><td class='user_info_tdi'><i class=\"fa fa-map-marker\"></i></td><td>".lang('lives_in')." $row_country<td></tr>";}
            if (!empty($row_birthday)){echo "<tr><td class='user_info_tdi'><i class=\"fa fa-calendar\"></i></td><td>".lang('born_on')." $row_birthday<td></tr>";}
            if (!empty($website_row)){echo "<tr><td class='user_info_tdi'><i class=\"fa fa-globe\"></i></td><td>$website_row<td></tr>";}
            ?>
            </table>
        </div>
        <div class="user_info">
            <p style="font-size: 18px;"><?php echo lang('bio');?></p><hr style="margin: 0;margin-bottom: 10px;">
            <?php
            if (!empty($row_bio)){echo "
                <p>$row_bio</p>
            ";}else{
            echo "
            <p>".lang('nothingToShow')."</p>
            ";
            }
            ?>
            </div>
            <?php
            if ($row_id == $_SESSION['id']) {
            ?>
            <div class="user_info">
            <p><span class="fa fa-lock"></span> <?php echo lang('my_notepad');?><br><label style="font-weight: normal;color: rgba(0, 0, 0, 0.31);font-size: small;"><?php echo lang('onlyUcanCThis');?></label>
            </p>
            <p class="profile_mynotepad_box">
            </p>
                <a href="../mynotepad/new" class="green_flat_btn"><?php echo lang('new_note');?></a>
                <a href="../mynotepad/" class="silver_flat_btn"><?php echo lang('see_all_notes');?></a>
            </div>
            <?php
            }
            ?>
        </div>
            <div class="profile_cneterCol_2">
<?php
switch (filter_var($_GET['ut'],FILTER_SANITIZE_STRING)) {
        case 'photos':
            ?>
<!--=============================================myphotos_section==================================================-->
            <div class="post" id="myphotos_section" style="padding: 5px;text-align:<?php echo lang('textAlign'); ?>;">
            <?php
            $u_id = $row_id;
            $emptyImg = '';
            $getphotos_sql = "SELECT * FROM wpost WHERE author_id=:u_id AND post_img != :emptyImg ORDER BY post_time DESC";
            $getphotos = $conn->prepare($getphotos_sql);
            $getphotos->bindParam(':u_id',$u_id,PDO::PARAM_INT);
            $getphotos->bindParam(':emptyImg',$emptyImg,PDO::PARAM_STR);
            $getphotos->execute();
            $getphotosCount = $getphotos->rowCount();
            ?>
            <p class="titleUserPhotosProfile"><?php echo lang('totalPhotos'); ?> <span><?php echo $getphotosCount; ?></span></p>
            <?php
            if ($getphotosCount < 1) {
                echo lang('nothingToShow');
            }
            while ($fetchMyPhotos = $getphotos->fetch(PDO::FETCH_ASSOC)) {
                $fetch_post_id = $fetchMyPhotos['post_id'];
                $fetch_author_id = $fetchMyPhotos['author_id'];
                $fetch_post_author = $fetchMyPhotos['post_author'];
                $fetch_post_author_photo = $fetchMyPhotos['post_author_photo'];
                $fetch_post_img = $fetchMyPhotos['post_img'];
                $fetch_post_time = $fetchMyPhotos['post_time'];
                $fetch_post_content = $fetchMyPhotos['post_content'];
                $timeago = time_ago($fetch_post_time);
                $fetch_post_status = $fetchMyPhotos['p_status'];
                
                $quesql = "SELECT * FROM signup WHERE id=:fetch_author_id";
                $query = $conn->prepare($quesql);
                $query->bindParam(':fetch_author_id', $fetch_author_id, PDO::PARAM_INT);
                $query->execute();
                while ($author_fetch = $query->fetch(PDO::FETCH_ASSOC)) {
                    $author_fetch_id = $author_fetch['id'];
                    $author_fetch_username = $author_fetch['Username'];
                    $author_fetch_fullname = $author_fetch['Fullname'];
                    $author_fetch_userphoto = $author_fetch['Userphoto'];
                    $author_fetch_verify = $author_fetch['verify'];
                }
            ?>
            <div class="userPhotosProfile">
                <img src="../imgs/<?php echo $fetch_post_img; ?>" />
            </div>
            <?php
            }   
            ?>
            </div>
<!--=============================================End myphotos_section==================================================-->
            <?php
            break;

        case 'followers':
?>
<!--=============================================followers_section==================================================-->
<div class="post" id="followers_section">
<?php
if ($_SESSION['id'] == $row_id) {
    $followers_paragraph = lang('uProf_urfollowersTitle');
}else{
    if (lang('uProf_followersTitleCheck') == 'ar') {
    $followers_paragraph = lang('uProf_followersTitle')." $row_fullname";
    }else{
    $followers_paragraph = "$row_fullname ".lang('uProf_followersTitle')."";
    }

}
?>
<p class="small_caps_paragraph" style="text-align:<?php echo lang('uProf_ffTitle_align'); ?>;"><?php echo $followers_paragraph; ?></p>
<?php
$s_id = $_SESSION['id'];
$getfollowers_sql = "SELECT * FROM follow WHERE uf_two=:row_id";
$getfollowers = $conn->prepare($getfollowers_sql);
$getfollowers->bindParam(':row_id',$row_id,PDO::PARAM_INT);
$getfollowers->execute();
$num_followers = $getfollowers->rowCount();
if ($num_followers == 0) {
echo "<p style='color:gray;padding:5px;margin:0;font-size:18px;text-align:center;'>".lang('nothingToShow')."</p>";
}else{
while ($getfollow = $getfollowers->fetch(PDO::FETCH_ASSOC)) {
$getfollow_id = $getfollow['uf_one'];
$ufollowers_sql = "SELECT * FROM signup WHERE id=:getfollow_id";
$ufollowers = $conn->prepare($ufollowers_sql);
$ufollowers->bindParam(':getfollow_id',$getfollow_id,PDO::PARAM_INT);
$ufollowers->execute();
if ($getfollow_id == $_SESSION['id']) {
    if ($_SESSION['verify'] == "1") {
     $verifypage_var = $verifyUser;
    }else{
     $verifypage_var = "";
    }
echo "
<table class='user_follow_box'>
<tr>
<td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/".$_SESSION['Userphoto']."\" alt=\"".$_SESSION['Fullname']."\" /></div></td>
<td style='width: 70%;'><a href=\"".$_SESSION['Username']."\" class='user_follow_box_a'><p>".$_SESSION['Fullname']." ".$verifypage_var."<br><span style='color:gray;'>@".$_SESSION['Username']."</span></a></td>
</tr>
</table>
";
}
while ($fetch_followers = $ufollowers->fetch(PDO::FETCH_ASSOC)) {
$id_followers = $fetch_followers['id'];
$fullname_followers = $fetch_followers['Fullname'];
$username_followers = $fetch_followers['Username'];
$userphoto_followers = $fetch_followers['Userphoto'];
$verify_followers = $fetch_followers['verify'];
$followBtn_sql = "SELECT id FROM follow WHERE uf_one=:s_id AND uf_two=:id_followers";
$followBtn = $conn->prepare($followBtn_sql);
$followBtn->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$followBtn->bindParam(':id_followers',$id_followers,PDO::PARAM_INT);
$followBtn->execute();
$followBtn_num = $followBtn->rowCount();
if ($followBtn_num > 0){
    $follow_btn = "<span id='followUnfollow_$id_followers' style='cursor:pointer'><button class=\"unfollow_btn\" onclick=\"followUnfollow('$id_followers')\"><span class=\"fa fa-check\"></span> ".lang('followingBtn_str')."</button></span>";
}else{
    $follow_btn = "<span id='followUnfollow_$id_followers' style='cursor:pointer'><button class=\"follow_btn\" onclick=\"followUnfollow('$id_followers')\"><span class=\"fa fa-plus-circle\"></span> ".lang('followBtn_str')."</button></span>";
}
if ($verify_followers == "1"){
$verifypage_var = $verifyUser;
}else{
$verifypage_var = "";
}
if($id_followers != $_SESSION['id']){
       echo "
<table class='user_follow_box'>
<tr>
<td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/$userphoto_followers\" alt=\"$fullname_followers\" /></div></td>
<td style='width: 70%;'><a href=\"$username_followers\" class='user_follow_box_a'><p>$fullname_followers $verifypage_var<br><span style='color:gray;'>@$username_followers</span></a></td>
<td style='width: 100%;'><span style='float:".lang('float2').";'>$follow_btn</span></td>
</tr>
</table>
";
}
}
}
}
?>
</div>
<!--=============================================End followers_section==================================================-->
<?php
break;
case 'following':
?>
<!--============================================followeing_section==================================================-->
<div class="post" id="followeing_section">
<?php
if ($_SESSION['id'] == $row_id) {
    $following_paragraph = lang('uProf_urfollowingTitle');
}else{
    if ($row_gender == "Male") {
        $genser_f = lang('uProf_followingTitleHe');
    }elseif($row_gender == "Female"){
        $genser_f = lang('uProf_followingTitleShe');
    }
    $following_paragraph = lang('uProf_followingTitle1')." $genser_f".lang('uProf_followingTitle2');
}
?>
<p class="small_caps_paragraph" style="text-align:<?php echo lang('uProf_ffTitle_align'); ?>;"><?php echo $following_paragraph; ?></p>
<?php
$s_id = $_SESSION['id'];
$getfolloweing_sql = "SELECT * FROM follow WHERE uf_one=:row_id";
$getfolloweing = $conn->prepare($getfolloweing_sql);
$getfolloweing->bindParam(':row_id',$row_id,PDO::PARAM_INT);
$getfolloweing->execute();
$num_followers = $getfolloweing->rowCount();
if ($num_followers == 0) {
echo "<p style='color:gray;padding:15px;margin:0;font-size:18px;text-align:center;'>".lang('nothingToShow')."</p>";
}else{
while ($getfolloweing_fetch = $getfolloweing->fetch(PDO::FETCH_ASSOC)) {
$getfolloweing_id = $getfolloweing_fetch['uf_two'];
$ufolloweing_sql = "SELECT * FROM signup WHERE id=:getfolloweing_id";
$ufolloweing = $conn->prepare($ufolloweing_sql);
$ufolloweing->bindParam(':getfolloweing_id',$getfolloweing_id,PDO::PARAM_INT);
$ufolloweing->execute();
if ($getfolloweing_id == $_SESSION['id']) {
    if ($_SESSION['verify'] == "1") {
     $verifypage_var = $verifyUser;
    }else{
     $verifypage_var = "";
    }
echo "
<table class='user_follow_box'>
<tr>
<td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/".$_SESSION['Userphoto']."\" alt=\"".$_SESSION['Fullname']."\" /></div></td>
<td style='width: 70%;'><a href=\"".$_SESSION['Username']."\" class='user_follow_box_a'><p>".$_SESSION['Fullname']." ".$verifypage_var."<br><span style='color:gray;'>@".$_SESSION['Username']."</span></a></td>
</tr>
</table>
";
}
while ($fetch_followeing = $ufolloweing->fetch(PDO::FETCH_ASSOC)) {
$id_followeing = $fetch_followeing['id'];
$fullname_followeing = $fetch_followeing['Fullname'];
$username_followeing = $fetch_followeing['Username'];
$userphoto_followeing = $fetch_followeing['Userphoto'];
$verify_followeing = $fetch_followeing['verify'];
$followBtn_sql = "SELECT id FROM follow WHERE uf_one=:s_id AND uf_two=:id_followeing";
$followBtn = $conn->prepare($followBtn_sql);
$followBtn->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$followBtn->bindParam(':id_followeing',$id_followeing,PDO::PARAM_INT);
$followBtn->execute();
$followBtn_num = $followBtn->rowCount();
if ($followBtn_num > 0){
    $follow_btn = "<span id='followUnfollow_$id_followeing' style='cursor:pointer'><button class=\"unfollow_btn\" onclick=\"followUnfollow('$id_followeing')\"><span class=\"fa fa-check\"></span> ".lang('followingBtn_str')."</button></span>";
}else{
    $follow_btn = "<span id='followUnfollow_$id_followeing' style='cursor:pointer'><button class=\"follow_btn\" onclick=\"followUnfollow('$id_followeing')\"><span class=\"fa fa-plus-circle\"></span> ".lang('followBtn_str')."</button></span>";
}
if ($verify_followeing == "1"){
$verifypage_var = $verifyUser;
}else{
$verifypage_var = "";
}
if($id_followeing != $_SESSION['id']){
       echo "
<table class='user_follow_box' id='UserUnfollow_$id_followeing'>
<tr>
<td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/$userphoto_followeing\" alt=\"$fullname_followeing\" /></div></td>
<td style='width: 70%;'><a href=\"$username_followeing\" class='user_follow_box_a'><p>$fullname_followeing $verifypage_var<br><span style='color:gray;'>@$username_followeing</span></a></td>
<td style='width: 100%;'><span style='float:".lang('float2').";'>$follow_btn</span></td>
</tr>
</table>
";
}
}
}
}
?>
</div>
<!--============================================End followeing_section==================================================-->
<?php
break;

case 'stars':
?>
<!--============================================ Stars_section==================================================-->
<div class="post">
<?php
$s_id = $_SESSION['id'];
$getS_sql = "SELECT * FROM r_star WHERE p_id =:row_id";
$getS = $conn->prepare($getS_sql);
$getS->bindParam(':row_id',$row_id,PDO::PARAM_INT);
$getS->execute();
$getS_count = $getS->rowCount();
if ($getS_count == 0) {
    echo "<p style='color:gray;padding:15px;margin:0;font-size:18px;text-align:center;'>".lang('nothingToShow')."</p>";
}else{
while ($getS_row = $getS->fetch(PDO::FETCH_ASSOC)) {
    $getuserid = $getS_row['u_id'];
    $getuser_sql = "SELECT * FROM signup WHERE id=:getuserid";
    $getuser = $conn->prepare($getuser_sql);
    $getuser->bindParam(':getuserid',$getuserid,PDO::PARAM_INT);
    $getuser->execute();
    if ($getuserid == $_SESSION['id']) {
    if ($_SESSION['verify'] == "1") {
     $verifypage_var = $verifyUser;
    }else{
     $verifypage_var = "";
    }
    echo "
    <table class='user_follow_box'>
    <tr>
    <td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/".$_SESSION['Userphoto']."\" alt=\"".$_SESSION['Fullname']."\" /></div></td>
    <td style='width: 70%;'><a href=\"".$_SESSION['Username']."\" class='user_follow_box_a'><p>".$_SESSION['Fullname']." ".$verifypage_var."<br><span style='color:gray;'>@".$_SESSION['Username']."</span></a></td>
    </tr>
    </table>
    ";
    }
    while ($getuser_row = $getuser->fetch(PDO::FETCH_ASSOC)) {
        $id_stars = $getuser_row['id'];
        $fullname_stars = $getuser_row['Fullname'];
        $username_stars = $getuser_row['Username'];
        $userphoto_stars = $getuser_row['Userphoto'];
        $verify_stars = $getuser_row['verify'];
        $followBtn_sql = "SELECT id FROM follow WHERE uf_one=:s_id AND uf_two=:id_stars";
        $followBtn = $conn->prepare($followBtn_sql);
        $followBtn->bindParam(':s_id',$s_id,PDO::PARAM_INT);
        $followBtn->bindParam(':id_stars',$id_stars,PDO::PARAM_INT);
        $followBtn->execute();
        $followBtn_num = $followBtn->rowCount();
        if ($followBtn_num > 0){
            $follow_btn = "<span id='followUnfollow_$id_stars' style='cursor:pointer'><button class=\"unfollow_btn\" onclick=\"followUnfollow('$id_stars')\"><span class=\"fa fa-check\"></span> ".lang('followingBtn_str')."</button></span>";
        }else{
            $follow_btn = "<span id='followUnfollow_$id_stars' style='cursor:pointer'><button class=\"follow_btn\" onclick=\"followUnfollow('$id_stars')\"><span class=\"fa fa-plus-circle\"></span> ".lang('followBtn_str')."</button></span>";
        }

        if ($verify_stars == "1"){
        $verifypage_var = $verifyUser;
        }else{
        $verifypage_var = "";
        }

        if($id_stars != $_SESSION['id']){
               echo "
        <table class='user_follow_box' id='UserUnfollow_$id_stars'>
        <tr>
        <td class='user_info_tdi'><div><img src=\"../imgs/user_imgs/$userphoto_stars\" alt=\"$fullname_stars\" /></div></td>
        <td style='width: 70%;'><a href=\"$username_stars\" class='user_follow_box_a'><p>$fullname_stars $verifypage_var<br><span style='color:gray;'>@$username_stars</span></a></td>
        <td style='width: 100%;'><span style='float:".lang('float2').";'>$follow_btn</span></td>
        </tr>
        </table>
        ";
        }
    }
}
}
?>
</div>
<!--============================================End Stars_section==================================================-->
<?php
break;

default:
?>
<!--===============================================posts_section====================================================-->
<div id="posts_section">
<?php echo $err_success_Msg; ?>
<?php
$s_id = $_SESSION['id'];
$emptyImg = '';
$getphotos_sql = "SELECT * FROM wpost WHERE author_id=:s_id AND post_img != :emptyImg ORDER BY post_time";
$getphotos = $conn->prepare($getphotos_sql);
$getphotos->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$getphotos->bindParam(':emptyImg',$emptyImg,PDO::PARAM_STR);
$getphotos->execute();
$getphotos_num = $getphotos->rowCount();
if ($_SESSION['id'] == $row_id) {
    include("../includes/w_post_form.php");
}
?>
<?php
if ($getphotos_num > 0) {
    include("../includes/myphotosProfile.php");
}
if ($posts_num_int < 1) {
if ($_SESSION['id'] == $row_id) {
echo "
<div class='post'>
<p style='color: gray;text-align: center;padding: 15px;margin: 0px;'>".lang('you_have_not_posted_anything_yet').".</p>
</div>
";
 }else{
echo "
<div class='post'>
<p style='color: gray;text-align: center;padding: 15px;margin: 0px;'>$row_fullname ".lang('has_not_posted_anything_yet').".</p>
</div>
";
 } 
}
?>

<!--========================================================================-->
                <div id="FetchingPostsDiv">
                </div>
                <div class="post loading-info" id="LoadingPostsDiv" style="padding: 8px;padding-bottom: 100px;">
                    <div class="animated-background">
                        <div class="background-masker header-top"></div>
                        <div class="background-masker header-left"></div>
                        <div class="background-masker header-right"></div>
                        <div class="background-masker header-bottom"></div>
                        <div class="background-masker subheader-left"></div>
                        <div class="background-masker subheader-right"></div>
                        <div class="background-masker subheader-bottom"></div>
                        <div class="background-masker content-top"></div>
                        <div class="background-masker content-first-end"></div>
                        <div class="background-masker content-second-line"></div>
                        <div class="background-masker content-second-end"></div>
                        <div class="background-masker content-third-line"></div>
                        <div class="background-masker content-third-end"></div>
                    </div>
                </div>
                <div class="post  loading-info" id="NoMorePostsDiv" style="display: none;">
                  <p style="color: #b1b1b1;text-align: center;padding: 15px;margin: 0px;font-size: 18px;"><?php echo lang('noMoreStories'); ?></p>
                </div>
                <div class="post  loading-info" id="LoadMorePostsBtn" style="display: none;">
                  <button class="blue_flat_btn" style="width: 100%" onclick="fetchPosts_DB('user')">Load more</button>
                </div>
                <input type="hidden" id="GetLimitOfPosts" value="0">

        </div>
<!--============================================End posts_section==================================================-->
<?php
    break;
    }
?>
<!--================================================================================================================-->
        </div>
        </div>
        </div>
<?php
}else{
?>
<style type="text/css">
body{
background: #fff;
}
.error_page_btn{
background: whitesmoke;
padding: 8px;
border-radius: 3px;
color: #6b6b6b;
text-decoration: none;
box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.05);
transition: background 0.1s , color 0.1s;
}
.error_page_btn:hover, .error_page_btn:focus{
background: #4a708e;
color: #fff;
text-decoration: none;
}
.error_div{
padding: 15px;
max-width: 800px;
color: #383838;
box-shadow: none;
border: 1px solid rgba(217, 217, 217, 0.36);
}
</style>
<div align="center" style="margin-top: 150px;margin-bottom: 150px;">
<div class="post error_div" align="center">
<h1 style="font-weight: bold;"><img src="../imgs/main_icons/1f915.png" style="width: 80px;height: 80px;" /> <?php echo lang('profilePageNotFound_str1'); ?></h1>
<h3><?php echo lang('profilePageNotFound_str2'); ?></h3><br>
<a href="javascript:history.back()" class="error_page_btn"><?php echo lang('profilePageNotFound_str3'); ?></a>
</div></div>
<?php
}
?>
<!--=================================================footer==========================================================-->
<?php include("../includes/footer.php"); ?>
    <?php include "../includes/endJScodes.php"; ?>
    </body>
</html>
