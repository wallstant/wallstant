<?php
$fileName = $_FILES["photo_field"]["name"];
$fileTmpLoc = $_FILES["photo_field"]["tmp_name"];
$fileType = $_FILES["photo_field"]["type"];
$fileSize = $_FILES["photo_field"]["size"]; 
$fileErrorMsg = $_FILES["photo_field"]["error"];
$fileName = preg_replace('#[^a-z.0-9]#i', '', $fileName); 
$kaboom = explode(".", $fileName);
$fileExt = end($kaboom);
$fileName = time().rand().".".$fileExt;
if(isset($_POST['submit_photo'])){
if (!$fileTmpLoc) {
echo "
<p id='error_msg' onclick='hide_notify()'>
Please select an image and then try again! 
</p>
";
    exit();
} else if($fileSize > 12242880) {
    echo "
<p id='error_msg' onclick='hide_notify()'>
Your image must be less than 12MB of size.
</p>
";
    unlink($fileTmpLoc); 
    exit();
} else if (!preg_match("/.(jpeg|jpg|png)$/i", $fileName) ) {  
echo "
<p id='error_msg' onclick='hide_notify()'>
Your image was not jpeg, jpg, or png file.
</p>
"; 
     unlink($fileTmpLoc);
     exit();
} else if ($fileErrorMsg == 1) {
    echo "
<p id='error_msg' onclick='hide_notify()'>
An error occured while processing the image. Try again.
</p>
";
    exit();
}
$moveResult = move_uploaded_file($fileTmpLoc, "../imgs/user_imgs/$fileName");
if ($moveResult != true) {
 echo "
<p id='error_msg' onclick='hide_notify()'>
ERROR: File not uploaded. Try again.
</p>
";
    exit();
}else{
    $s_un = $_SESSION['Username'];
    $uploaded_sql = "UPDATE signup SET Userphoto= :fileName WHERE Username= :s_un";
    $uploaded_q = $conn->prepare($uploaded_sql);
    $uploaded_q->bindParam(':fileName',$fileName,PDO::PARAM_STR);
    $uploaded_q->bindParam(':s_un',$s_un,PDO::PARAM_STR);
    $uploaded_q->execute();
    $_SESSION['Userphoto'] = $fileName;
echo "<meta http-equiv='refresh' content='0;' />";
}
}
?>