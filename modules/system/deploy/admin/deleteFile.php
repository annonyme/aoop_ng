<?php
use core\net\XWRequest;
use core\pages\plain\XWPage;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 21.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();
 if($_SESSION["XWUSER"]->isInGroup("pageAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){
     $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
 	 if(isset($request["pageName"])){     
	     $page=new XWPage();
	     $page->load($request["pageName"],$pageDir);
	     $page->delete();
	          
	     ?>
 	     <div class="PresentationBoxHeader">Delete Page:</div>
         <div class="PresentationBox">Page was deleted.</div>
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
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=pagesList&adminpage=1">Pages-Administration</a></div>
