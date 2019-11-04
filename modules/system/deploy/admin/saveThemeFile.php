<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;

/*
 * Created on 13.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();
 if($_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
 	 if(isset($_FILES["upfile"]) && preg_match("/(\.css)|(\.html)|(\.js)|(\.dooptemplate)/i",$_FILES["upfile"]["name"])){
	     
	     $imgfile=$_FILES["upfile"]["tmp_name"];
         $finalFilename=$_FILES["upfile"]["name"];
         copy($imgfile,"themes/".$request["upfolder"].$finalFilename);	     
	     ?>
 	     <div class="PresentationBoxHeader">Upload Theme-File:</div>
         <div class="PresentationBox">Theme-File was uploaded. back to <a href="index.php?page=<?=$request["page"] ?>&sub=themes&adminpage=1">Images-Administration</a></div>
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
