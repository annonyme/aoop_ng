<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWUser;

/*
 * Created on 10.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 if($_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
 	 if(XWRequest::instance()->exists("userId")){
	     $user=new XWUser();
	     $user->load(XWRequest::instance()->getInt("userId"));
	     if(XWRequest::instance()->getString("state")){
	     	$user->setActive(XWRequest::instance()->getString("state")=="active");
	     }
	     $user->save();	     
	     ?>
 	     <div class="PresentationBoxHeader">User Status:</div>
         <div class="PresentationBox">User-Status was set. back to <a href="index.php?page=<?=XWRequest::instance()->getString("page") ?>&sub=usersList&adminpage=1">Users-Administration</a></div>
 	     <?php
     }
     else{
     	 ?>
 	     <div class="WarningBoxHeader">no user id</div>
         <div class="WarningBox">no user id found!</div>
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
