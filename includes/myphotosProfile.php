<div class="post" style="padding: 0;">
<div class="myPhotosProfile">
	<p style='text-align: <?php echo lang('my_photos_align');?>'><img src="../imgs/main_icons/1f3d5.png"> <a href="<?php echo $row_username;?>&ut=photos" id="myphotos_btn"><?php if($_SESSION['Username'] == $row_username){echo lang('my_photos');}else{echo lang('user_photos');}?></a></p>
	<div style='display: flex;'>
<?php
$u_id = $row_id;
$emptyImg = '';
$getphotos_sql = "SELECT * FROM wpost WHERE author_id=:u_id AND post_img != :emptyImg ORDER BY post_time DESC LIMIT 3";
$getphotos = $conn->prepare($getphotos_sql);
$getphotos->bindParam(':u_id',$u_id,PDO::PARAM_INT);
$getphotos->bindParam(':emptyImg',$emptyImg,PDO::PARAM_STR);
$getphotos->execute();

$getphotos2_sql = "SELECT * FROM wpost WHERE author_id=:u_id AND post_img != :emptyImg ORDER BY post_time";
$getphotos2 = $conn->prepare($getphotos2_sql);
$getphotos2->bindParam(':u_id',$u_id,PDO::PARAM_INT);
$getphotos2->bindParam(':emptyImg',$emptyImg,PDO::PARAM_STR);
$getphotos2->execute();
$getphotos2_num = $getphotos2->rowCount();

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
echo "
<div>
    	<img src=\"../imgs/$fetch_post_img\">
    </div>
";
}	
if ($getphotos2_num < 3 or $getphotos2_num == 3) {	
}else{
	$moreNum = thousandsCurrencyFormat($getphotos2_num-3);
?>
<a href="<?php echo $row_username;?>&ut=photos" id="myphotos_btn">
<div style="text-align: center;">
<br>
<span><?php echo "+$moreNum"; ?></span>
</div>
</a>
<?php
 }
?>
</div>
</div>
</div>
<p style="text-align: <?php echo lang('all_postsSTR_align');?>" class='ProfileAllPostP'><?php echo lang('all_postsSTR');?></p>