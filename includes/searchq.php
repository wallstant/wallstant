<?php
session_start();
include('../config/connect.php');
if($_POST){
$q = htmlentities($_POST['search_user'], ENT_QUOTES);
$dircheckPath = htmlentities($_POST['dircheckPath'], ENT_QUOTES);
// ============================ get posts from search request =====================================
$search_sql = "SELECT * FROM signup WHERE (Fullname LIKE ? OR country LIKE ?) OR (CONCAT(work,?,work0) LIKE ?) OR Username LIKE ? LIMIT 8";
$params = array("$q%", "$q%", " ", "$q%", "$q%");
$search = $conn->prepare($search_sql);
$search->execute($params);
$search_num = $search->rowCount();
if ($search_num == 0) {
echo "
<div id='sqresultItem'>
<a href='#'><div style='border-top: 1px solid rgba(0, 0, 0, 0.1);display: inline-flex;width: 100%;'><div class='navbar_fetchBoxUser'><img src='".$dircheckPath."imgs/main_icons/2139.png' /></div><p>".lang('not_found')."<br><span style='font-size: small;'>".lang('no_users_like_the_name_you_entered').".</span></p></div></a>
</div>
";
}elseif ($search_num >= 1) {
while($row = $search->fetch(PDO::FETCH_ASSOC)){
$fullname = $row['Fullname'];
$username = $row['Username'];
$id = $row['id'];
$userphoto = $row['Userphoto'];
$verify = $row['verify'];
$country = $row['country'];
$work = $row['work'];
$work0 = $row['work0'];
if ($verify == "1") {
	$verify_s = $verifyUser;
}else {
    $verify_s = "";
}

if ($country == "") {
	$under_fullname = "@".$username;
}else{
if($work0 == ""){
     $under_fullname = $country;
}else{
	$under_fullname = $work0." &bull; ".$country;
}
}
echo "
<div id='sqresultItem'>
<a href='".$dircheckPath."u/$username'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser'><img src='".$dircheckPath."imgs/user_imgs/$userphoto' /></div>
<p>$fullname $verify_s<br><span style='font-size: small;'>$under_fullname</span></p>
</div>
</a>
</div>";
}
}
if (strlen($q) > 20) {
	$substrQuery = substr($q, 0,20)."..";
}else{
	$substrQuery = $q;
}
// ============================ count public posts that have hashtag requested =====================================
$ht_publicPosts_sql = "SELECT post_content FROM wpost WHERE post_content LIKE ? AND p_privacy != ? AND p_privacy != ?";
$ht_publicPosts_params = array("%#$q%", "1", "2");
$ht_publicPosts = $conn->prepare($ht_publicPosts_sql);
$ht_publicPosts->execute($ht_publicPosts_params);
$ht_publicPostsCount = $ht_publicPosts->rowCount();
echo "
<div id='sqresultItem'>
<a href='".$dircheckPath."hashtag/$q'><div style='border-top: 1px solid rgba(0, 0, 0, 0.1);display: inline-flex;width: 100%;'><div class='navbar_fetchBoxUser'><img src='".$dircheckPath."imgs/main_icons/0023.png' /></div><p>#".$substrQuery."</b>"."<br><span style='font-size: small;'>$ht_publicPostsCount ".lang('publicPosts')."</span></p></div></a>
</div>
";
echo "
<div id='sqresultItem'>
<a href='".$dircheckPath."search?q=$q'><div style='border-top: 1px solid rgba(0, 0, 0, 0.1);display: inline-flex;width: 100%;'><div class='navbar_fetchBoxUser'><img src='".$dircheckPath."imgs/main_icons/1f310.png' /></div><p>".lang('searchMoreAbout')." ".'"'."<b>".$substrQuery."</b>".'"'."<br><span style='font-size: small;'>".lang('advancedSearch')."</span></p></div></a>
</div>
";
}
?>