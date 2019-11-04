<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 18.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
 $urlparts=preg_split("/\//",$_SERVER["PHP_SELF"]);
 $curUrl="";
 for($i=0;$i<count($urlparts)-1;$i++){
     $curUrl.=$urlparts[$i]."/";
 }
 $thisPageURL="http://".$_SERVER["HTTP_HOST"].$curUrl;
 */
$request = XWRequest::instance()->getRequestAsArray();

 $itk=XWServerInstanceToolKit::instance();
 //$switcher=new XWServerSwitch($itk->getCurrentInstanceURLWithParameters()); 
 $switcher=$itk->getServerSwitch();
 if($_SESSION["XWUSER"]->isInGroup("admins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
 	 if(isset($request["pages"])){
	     if($_SESSION["XWUSER"]->isInGroup("admins")){
	     	$switcher->setPages($request["pages"]);
	     	$switcher->setAddons($request["addons"]);
	     	$switcher->setAdmins($request["admins"]);
	     }	     
	     $switcher->setTheme($request["template"]);	     
	     $switcher->setHomepage($request["homepage"]);
	     $switcher->setKeywords($request["keywords"]);
	     $switcher->save();
	     ?>
 	     <div class="PresentationBoxHeader">Save Instance-XML:</div>
         <div class="PresentationBox">Instance deploy.xml was saved. back to <a href="index.php?page=<?=$request["page"] ?>&sub=instance&adminpage=1">Instance-Administration</a></div>
 	     <?php
     }
     else{
     	 ?>
 	     <div class="WarningBoxHeader">no request-arguments</div>
         <div class="WarningBox">no request-arguments found!</div>
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
