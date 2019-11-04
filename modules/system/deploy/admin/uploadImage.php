<?php
use core\net\XWRequest;
use core\utils\config\GlobalConfig;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 26.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();
 if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){
 	 if(isset($_FILES["upfile"]) && preg_match("/(\.jp(e)?g)|(\.png)|(\.bmp)|(\.gif)/i",$_FILES["upfile"]["name"])){
	     
	     $imgfile=$_FILES["upfile"]["tmp_name"];
         $finalFilename=$_FILES["upfile"]["name"];
         
         $realPath=preg_replace("/___/i","/",$request["upfolder"]);
         $realPath=preg_replace("/(\/{2)|(\.{2)/i","",$realPath);

         $currentInstance = XWServerInstanceToolKit::instance()->getCurrentInstanceName();
         $root = \core\utils\config\GlobalConfig::instance()->getValue("imagesfolder") . $currentInstance . "/uploads/";
         copy($imgfile,$root.$realPath.$finalFilename);
	     ?>
 	     <div class="PresentationBoxHeader">Upload Image:</div>
         <div class="PresentationBox">Image was uploaded. back to <a href="index.php?page=<?=$request["page"] ?>&sub=images&adminpage=1&showFolder=<?=$request["upfolder"] ?>">Images-Administration</a></div>
 	     <?php
     }
     else{
     	 ?>
 	     <div class="WarningBoxHeader">no filename</div>
         <div class="WarningBox">no filename found!</div>
 	     <?php
     } 
 }
 else{
 	?>
 	<div class="WarningBoxHeader">Access denied</div>
    <div class="WarningBox">Admin-Rights needed!</div>
 	<?php
 }
?>
