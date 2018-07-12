<?php
$url = "$body";
if (is_dir("../imgs/")) {
    $pathCheck = "../";
}elseif (is_dir("imgs/")) {
    $pathCheck = "";
}

if ($page == "post") {
    $body2 = "$body";
}else{
    if (strlen($body) > 300) {
        $body2 = substr($body, 0,300)."... <a href='".$check_path."posts/post?pid=$get_post_id'>".lang("continue_reading")."</a>";
    }else{
        $body2 = "$body";
    }
}

if(preg_match("/youtu.be\/[a-z1-9.-_]+/", $url)) {
    preg_match("/youtu.be\/([a-z1-9.-_]+)/", $url, $check);
    if(isset($check[1])) {
        $url = 'http://www.youtube.com/embed/'.$check[1];
        $loading = "<img src='".$pathCheck."imgs/loading_video.gif' />";
        echo $body2."<br/>".'<div class="loading_ytv"><iframe width="100%" height="350px" src="'.$url.'" ></iframe></div>';
    }
}
else if(preg_match("/youtube.com(.+)v=([^&]+)/", $url)) {
    preg_match("/v=([^&]+)/", $url, $check);
    if(isset($check[1])) {
        $url = 'http://www.youtube.com/embed/'.$check[1];
        $loading = "<img src='".$pathCheck."imgs/loading_video.gif' />";
        echo $body2."<br/>".'<div class="loading_ytv"><iframe width="100%" height="350px" src="'.$url.'"></iframe></div>';
    }
}else{
    echo $body2;
}
?>
