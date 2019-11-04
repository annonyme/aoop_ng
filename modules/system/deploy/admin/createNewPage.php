<?php
use core\addons\XWAddonManager;
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
if(($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins"))  && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
    $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
 	if(isset($request["newPage"])){
	    $templateText="<div class=\"well\">New Page: " . $request["newPage"] . "</div>";

	    if(!file_exists($pageDir. '/' .$request["newPageCallname"].".html")){
            $page=new XWPage();
            $page->setPath($pageDir. '/' .$request["newPageCallname"].".html");
            $page->setName($request["newPage"]);
            $page->setCallName($request["newPageCallname"]);
            $page->setHidden(true);
            $page->setContent($templateText);
            $page->setUserId($_SESSION["XWUSER"]->getId());

            if(isset($request["newPageParent"])){
                $page->setParentPage($request["newPageParent"]);
            }

            $page->save();
        }
	    
	    if(file_exists($pageDir. '/' .$request["newPageCallname"].".html")){
		    ?>
            <div class="panel panel-default">
	 	        <div class="panel-heading">Create Page:</div>
	            <div class="panel-body">Page [<strong><?=$page->getName() ?></strong>] was created. back to <a href="index.php?page=<?=$request["page"] ?>&sub=pagesList&adminpage=1">Pages-Administration</a></div>
            </div>
	 	    <?php
	    }
	    else{
		    ?>
	 	    <div class="WarningBoxHeader">no file</div>
	        <div class="WarningBox">no file created!</div>
	 	    <?php	
	    }
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
