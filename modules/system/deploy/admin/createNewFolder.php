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
 	 if(XWRequest::instance()->exists("newFolder") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){	     
	     $newFolder=preg_replace("/(\/{2})|(\.{2})/","",XWRequest::instance()->getString("newFolder"));
	     $root = GlobalConfig::instance()->getValue("imagesfolder") . XWServerInstanceToolKit::instance()->getCurrentInstanceName() . "/";
	     if(!file_exists($root.$newFolder)){
	         mkdir($root.$newFolder, 0777, true);
	     }	     
	     ?>
 	     <div class="PresentationBoxHeader">Create Folder:</div>
         <div class="PresentationBox">Folder [<strong><?=$newFolder ?></strong>] was created.</div>
 	     <?php
     }
     else{
     	 ?>
 	     <div class="WarningBoxHeader">no foldername</div>
         <div class="WarningBox">no foldername found!</div>
 	     <?php
     } 
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?page=<?=XWRequest::instance()->getString("page") ?>&sub=images&adminpage=1">Images-Administration</a></div>
