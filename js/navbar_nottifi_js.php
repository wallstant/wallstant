<script type="text/javascript">
// ============================================ search ============================================
$(".navbar_search").keyup(function() {
var sbar = $(this).val();
var dircheckPath = "<?php echo $dircheckPath; ?>";
if(sbar == ''){
$("#getSearchResult").hide();
}
else{
$.ajax({
type: "POST",
url: "<?php echo $dircheckPath; ?>includes/searchq.php",
data: {'search_user':sbar,'dircheckPath':dircheckPath},
cache: false,
beforeSend:function(){
    $('#LoadingSearchResult').show();
    $('#getSearchResult').hide();
},
success: function(html){
$('#LoadingSearchResult').hide();
$("#getSearchResult").html(html).show();
}});
}return false; 
});
$("#searchq").click(function(e){
    $("#search_r").show();
     e.stopPropagation();
});
$("#search_r").keyup(function(e){
    e.stopPropagation();
});
// ============================================ notifications function ============================================
function getNotifications(obj,reqToFetch){
var path = "<?php echo $dircheckPath; ?>";
var whatF = "fetch";
var load = $('#'+obj+'_load').val();
$.ajax({
type: "POST",
url: "<?php echo $dircheckPath; ?>includes/fetch_notifybox.php",
data: {'what':whatF,'path':path,'load':load},
cache: false,
beforeSend:function(){
    if (reqToFetch == "getNew") {
        $("#"+obj+"_data").html('');
    }
    $("#"+obj+"_loading").show();
    $("#notifi_loadmoreBtn").hide();
},
success: function(data){
    if (data == "0"){
        $("#"+obj+"_noMore").show();
    }else{
        if (reqToFetch == "loadMore") {
            $("#"+obj+"_data").append(data);
        }
        if (reqToFetch == "getNew") {
            $("#"+obj+"_data").html(data);
        }
        $("#notifi_loadmoreBtn").show();
    }
    
    $("#"+obj+"_loading").hide();
    document.getElementById(obj+"_load").value = Number(load)+10;
}});
}
// ============================================ on click notification ============================================
$("#nav_Noti_Btn").click(function(e){
$("#notifications_box").show();
if ($('#notifications_r').attr("data-load") == "0"){
getNotifications('notifications','getNew');
$('#notifications_r').attr("data-load","1");
}
$("#notificationsCount").html('');
$('#nav_newNotify').attr('data-show','0');
$('#nav_newNotify').hide();
e.stopPropagation();

});
$("#nav_Noti_Btn").keyup(function(e){
e.stopPropagation();
});
// ============================================ notifications on scroll >> loadmore ============================
$("#notifications_rP").scroll(function(){
    var o = document.getElementById('notifications_rP');
    if(o.offsetHeight + o.scrollTop + 1 >= o.scrollHeight){
        getNotifications('notifications','loadMore');
    }
});
// ============================================ check notifications ============================================
function chNoti(){
var path = "<?php echo $dircheckPath; ?>";
var whatCh = "check";
$.ajax({
type: "POST",
url: "<?php echo $dircheckPath; ?>includes/fetch_notifybox.php",
data: {'what':whatCh,'path':path},
cache: false,
success: function(data){
if (data != '0') {
$("#notificationsCount").html("<span class='redAlert_notify_msgs'>"+data+"</span>");
if ($('#nav_newNotify').attr('data-show') != data) {
if ($('#nav_newNotify').attr('data-show') < data) {
$('#notifications_r').attr("data-load","0");
$('#notifications_load').val(0);
$('#nav_newNotify').html("<div class='notiAlert'><span class='fa fa-bell'></span> "+data+"</div>");
$('#nav_newNotify').fadeIn(function(){
var soundPath = path+"media/mp3/notification_bell3";
$('#nSound').html('<audio autoplay="autoplay"><source src="' + soundPath + '.mp3" type="audio/mpeg" /><embed hidden="true" autostart="true" loop="false" src="' + soundPath +'.mp3" /></audio>');
}).delay(5000).fadeOut(function(){
$('#nSound').html('');
$('#nav_newNotify').html("");
});
}
$('#nav_newNotify').attr('data-show',data);
}
}else{
$("#notificationsCount").html('');  
$('#nav_newNotify').attr('data-show','0');
}
}});
}
chNoti();
// ============================================ check new Messages ============================================
function chNewMsgs(){
var requ = "checkUnseenMsgs";
var path = "<?php echo $dircheckPath; ?>";
$.ajax({
    type:'POST',
    url:"<?php echo $dircheckPath; ?>includes/m_requests.php",
    data:{'req':requ,'path':path},
    success:function(data){
     $('#messagesCount').html(data);
    }
});
}
chNewMsgs();
// setInterval every 5 sec
function refRequests(){
    chNewMsgs();
    chNoti();
}
setInterval(refRequests,5000);
// ============================================ document codes ============================================
$(document).click(function(){
$("#search_r").hide();
$("#notifications_box").hide();
});
// ========================================================================================================
</script>