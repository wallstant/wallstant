<?php
session_start();
include('../config/connect.php');
include ("num_k_m_count.php");
$getTriendsPagesSql = "SELECT * FROM signup ORDER BY followers DESC LIMIT 6";
$getTriendsPages = $conn->prepare($getTriendsPagesSql);
$getTriendsPages->execute();
?>
<div id="trPages">
<?php
while ($fetchPages = $getTriendsPages->fetch(PDO::FETCH_ASSOC)) {
if ($fetchPages['verify'] == '1') {
	$PageVerifyBadge = $verifyUser;
}else{
	$PageVerifyBadge = "";
}
 ?>
<a href="u/<?php echo $fetchPages['Username']; ?>" class="TriendingPages_link">
<table class='TriendingPages'>
	<tr>
		<td style="width: 42px;"><div><img src="imgs/user_imgs/<?php echo $fetchPages['Userphoto']; ?>"></div></td>
		<td style="padding: 6px 0px;"><?php echo $fetchPages['Fullname']; ?> <?php echo $PageVerifyBadge; ?>
		<br><span style="font-weight: normal;font-size: 13px; color: #999;">@<?php echo $fetchPages['Username']; ?></span></td>
	</tr>
</table>
</a>

<?php
}
?>
</div>
<div id="trPosts" style="display: none;">
<?php
$emptypost = "";
$public = "0";
$getTriendsPostsSql = "SELECT * FROM wpost WHERE post_content!= :emptypost AND p_privacy = :public ORDER BY p_likes DESC LIMIT 6";
$getTriendsPosts = $conn->prepare($getTriendsPostsSql);
$getTriendsPosts->bindParam(':emptypost',$emptypost,PDO::PARAM_STR);
$getTriendsPosts->bindParam(':public',$public,PDO::PARAM_INT);
$getTriendsPosts->execute();
while ($fetch = $getTriendsPosts->fetch(PDO::FETCH_ASSOC)) {
$authorOfPost = $fetch['author_id'];
$PostContentTrending = $fetch['post_content'];
    $aop_trend_sql = "SELECT * FROM signup WHERE id=:authorOfPost";
    $aop_trend = $conn->prepare($aop_trend_sql);
    $aop_trend->bindParam(':authorOfPost', $authorOfPost, PDO::PARAM_INT);
    $aop_trend->execute();

    while ($fetchAuthor = $aop_trend->fetch(PDO::FETCH_ASSOC)) {
        $fetchAuthor_id = $fetchAuthor['id'];
        $fetchAuthor_username = $fetchAuthor['Username'];
        $fetchAuthor_fullname = $fetchAuthor['Fullname'];
    }
    if (strlen($PostContentTrending) > 70) {
        $PostContentTrending = substr($PostContentTrending, 0,70)."<b> ...</b>";
    }
echo "
<table class='TriendingPosts' style='width:100%;'>
<tr>
<td style='width: 40px;'><span class='fa fa-line-chart'></span></td>
<td><a href='posts/post?pid=".$fetch['post_id']."'><p>$PostContentTrending</p></a></td>
</tr>
<tr>
<td></td>
<td style='color: #03a9f4;font-size:11px;font-weight: normal;padding-top:0;'>@".$fetchAuthor_username."</td>
</tr>
</table>
";
}
?>
</div>

