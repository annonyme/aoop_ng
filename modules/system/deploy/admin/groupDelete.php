<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWGroup;

/*
 * Created on 18.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray(); 
if(isset($request["groupId"]) && $_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
   $group=new XWGroup();
   $group->load($request["groupId"]); 
   
   if($group->getName()!="admins"){
       $group->delete();
   }
   
   ?>
   <div class="PresentationBoxHeader">Deleted</div>
   <div class="PresentationBox">Group <strong><?=$group->getName() ?></strong> deleted.</div>
   <?php
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
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=groups&adminpage=1">Groups-Administration</a></div>
