<?php
use core\addons\XWAddonManager;
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

 if($_SESSION["XWUSER"]->isInGroup("admins")){
 	 if(isset($request["imageName"]) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
	     $realPath=preg_replace("/___/i","/",$request["subdir"]);
  		 $realPath=preg_replace("/(\/{2)|(\.{2)/i","",$realPath);

         $currentInstance = XWServerInstanceToolKit::instance()->getCurrentInstanceName();
         $root = \core\utils\config\GlobalConfig::instance()->getValue("imagesfolder") . $currentInstance . "/uploads/";
	     unlink($root.$realPath.$request["imageName"]);
	     
	     $back="";
	     if($request["subdir"]!=""){
	     	$back="&showFolder=".$request["subdir"];
	     }
	     ?>
 	     <div class="PresentationBoxHeader">Delete Image:</div>
         <div class="PresentationBox">Image ("<?=$request["subdir"].$request["imageName"] ?>") was deleted.</div>
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
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=images&adminpage=1<?=$back ?>">Images-Administration</a></div>

