<?php
if (is_dir("imgs/")) {
    $dircheckPath = "";
}elseif (is_dir("../imgs/")) {
    $dircheckPath = "../";
}elseif (is_dir("<?php echo $dircheckPath; ?>imgs/")) {
    $dircheckPath = "<?php echo $dircheckPath; ?>";
}
// ================================ check user exist ===================================
session_start();
$mys = $_SESSION['Username'];
$uCheckSession_sql = "SELECT Username FROM signup WHERE Username=:mys";
$uCheckSession = $conn->prepare($uCheckSession_sql);
$uCheckSession->bindParam(':mys',$mys,PDO::PARAM_STR);
$uCheckSession->execute();
$uCheckSessionCount = $uCheckSession->rowCount();
if ($uCheckSessionCount == 0) {
    session_unset();
    session_destroy();
}
?>
<style type="text/css">
    .navbar-nav>li{
        float: <?php echo lang('float'); ?>;
    }
</style>
<nav class="navbar navbar-inverse" style="position:fixed;top:0;right:0;left:0;z-index: 2;">
    <div class="container-fluid" style="width: 1040px;">
        <div class="navbar-header" style="float: <?php echo lang('float'); ?>;">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $dircheckPath; ?>home">Wallstant</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="<?php echo lang('ul_navbar_nav1'); ?>">
                <?php
                if (is_file("home.php")) {
                    $homePath = "home";
                }elseif (is_file("../home.php")) {
                    $homePath = "../home";
                }elseif (is_file("../../home.php")) {
                    $homePath = "../../home";
                }
                ?>
                <li><a href="<?php echo $homePath; ?>" style="padding: 14px 10px;"><span style="font-size: 17px;" class="fa fa-home"></span> <?php echo lang('navbar_home'); ?></a></li>
                <li><a href="javascript:void(0);" style="padding: 16px 10px;" id="nav_Noti_Btn"><span class="fa fa-bell"></span> <?php echo lang('notifications'); ?> <span id="notificationsCount"></span></a>
                <div class="navbar_fetchBox" id="notifications_box">
                <div style="position:relative;padding: 5px 10px;border-bottom: 1px solid #ccc;text-align: <?php echo lang('textAlign'); ?>"><?php echo lang('notifications'); ?>
                    <span class="toTopArrow" span class='toTopArrow' style="position: absolute; top: -10px;<?php echo lang('float'); ?>:8px;"></span>
                </div>
                <div id="notifications_rP" class="scrollbar" style="max-height: 450px; overflow-y: scroll;">
                    <div id="notifications_r" data-load="0">
                        <div id="notifications_data"></div>
                        <p style='width: 100%;border:none;display: none' id="notifications_loading" align='center'><img src='<?php echo $dircheckPath; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>
                        <p id="notifications_noMore" style='display:none;color:#9a9a9a;font-size:14px;text-align:center;'><?php echo lang('no_notifications'); ?></p>
                        <input type="hidden" id="notifications_load" value="0"> 
                    </div>
                </div>
                <div id='sqresultItem' align='center' style='background: #efefef; border: 1px solid #e0e0e0;'>
                <a href='<?php echo $dircheckPath; ?>notifications'>
                <div style='display: inline-flex;width: 100%;'>
                <p style='font-size:13px;'><?php echo lang('see_all'); ?>
                </p>
                </div>
                </a>
                </div>
                </div>
                <div id="nav_newNotify" data-show='0'></div>
                </li>
                <li><a href="<?php echo $dircheckPath; ?>messages/" style="padding: 16px 10px;"><span class="fa fa-envelope"></span> <?php echo lang('messages'); ?> <span id="messagesCount"></span></a>
                </li>
            </ul>
            <ul class="<?php echo lang('ul_navbar_nav2'); ?>">
                <li>
                <input id="searchq" dir="auto" class="navbar_search" type="search" name="navbar_search" placeholder="<?php echo lang('navbar_serchBox_ph'); ?>" style="text-align: <?php echo lang('textAlign'); ?>;" />
                </li>
                 <div class="navbar_fetchBox" id="search_r">
                 <div  id="getSearchResult" class="scrollbar" style="overflow: auto;max-height: 450px;"></div>
                 <p  id="LoadingSearchResult" style="background: url(imgs/loading_video.gif) center center no-repeat;width: 100%;height: 80px;margin: 0px;display: none;"></p>
                </div>
                <li class="dropdown"><a href="<?php echo $dircheckPath; ?>u/<?php echo $_SESSION['Username']; ?>" style="padding-bottom: 0px;padding-top: 9px;" class="dropdown-toggle" data-toggle="dropdown">
                        <div style="width: 36px;height: 36px;border-radius: 100%;overflow: hidden;background: #fff;">
                            <img style="width: auto;height: 100%;" src="<?php echo $dircheckPath.'imgs/user_imgs/'.$_SESSION['Userphoto']; ?>">
                        </div></a>
                    <ul class="dropdown-menu" style="margin-top: 8px;text-align: <?php echo lang('textAlign');?>">
                     <li class='dropdown-header'><?php echo lang('navbar_uMenu_UserProfile'); ?></li>
                        <li><a href="<?php echo $dircheckPath; ?>u/<?php echo $_SESSION['Username']; ?>">
                               <table style="width: 200px; font-size: 15px;">
                                   <tr>
                                       <td style="width: 32px;">
                                           <div style="border: 1px solid rgba(0, 0, 0, 0.1);width: 32px;height: 32px;overflow: hidden;border-radius:2px;">
                                               <img style="width:auto;height:100%;" src="<?php echo $dircheckPath.'imgs/user_imgs/'.$_SESSION['Userphoto']; ?>">
                                           </div>
                                       </td>
                                       <td style="padding: 0px 5px"><?php echo $_SESSION['Fullname']."<br><span style='color: #848484; font-size: 13px;'>@".$_SESSION['Username']."</span>"; ?></td>
                                   </tr>
                               </table>
                            </a></li>
                        <?php
                        if ($_SESSION['admin'] == "1" or $_SESSION['admin'] == "2"){
                            echo "
                            <li class=\"divider\"></li>
                            <li class='dropdown-header'>".lang('adminOptions')."</li>
                        <li><a href=\"".$dircheckPath."toacpnlp/?adb=General\"> ".lang('dashboard')."</a></li>
                            ";
                        }
                        ?>
                        <li class="divider"></li>
                        <li><a href="<?php echo $dircheckPath; ?>settings"> <?php echo lang('settings'); ?></a></li>
                        <li><a href="#" data-toggle="modal" data-target="#alertModal"> <?php echo lang('terms'); ?></a></li>
                        <li><a href="#" data-toggle="modal" data-target="#alertModal"> <?php echo lang('privacyPolicy'); ?></a></li>
                        <li><a href="<?php echo $dircheckPath; ?>page/supportbox"> <?php echo lang('supportBox'); ?></a></li>
                        <li><a href="<?php echo $dircheckPath; ?>page/report"> <?php echo lang('Report_A_Problem'); ?></a></li>
                        <li><a href="<?php echo $dircheckPath; ?>logout"> <?php echo lang('logout'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div style="" id='nSound'></div>
<?php include ($dircheckPath."js/navbar_nottifi_js.php"); ?>