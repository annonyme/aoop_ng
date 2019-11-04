<?php
use core\net\XWRequest;
use core\pages\plain\XWPageSaver;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 21.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();
 if($_SESSION["XWUSER"]->isInGroup("pagesAdmin") || $_SESSION["XWUSER"]->isInGroup("admins")){
     $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
 	 if(isset($request["pageName"])){
	     if(isset($request["newfileName"])){
	     	 $saver=new XWPageSaver($pageDir,$request["pageName"]);
	         $saver->saveNewContent($saver->getCurrentContent(),$_SESSION["XWUSER"]->getId());
	     	 
	     	 rename($pageDir.$request["pageName"],$pageDir.$request["newfileName"]);
	     	 //XWPage.. copy.. save.. delete old with backup
	     	 ?>
 	         <div class="PresentationBoxHeader">Rename Page:</div>
             <div class="PresentationBox">Page was renamed 
             (<strong>[<?=$request["pageName"] ?>] to [<?=$request["newfileName"] ?>]</strong>). 
             back to <a href="index.php?page=<?=$request["page"] ?>&sub=pagesList&adminpage=1">Pages-Administration</a></div>
 	         <?php
	     }
	     else{
	     ?>
 	     <div class="PresentationBoxHeader">Rename Page:</div>
         <div class="PresentationBox">
           <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=renameFile&adminpage=1&pageName=<?=$request["pageName"] ?>">
           <strong>new Name:</strong> <input type="text" name="newfileName" value="<?=$request["pageName"] ?>"/><br/>
           <input type="submit" class="submit" value="rename"/>
           </form>
         </div>
 	     <?php	
	     }	     
     }
     else{
     	 ?>
 	     <div class="WarningBoxHeader">no filename</div>
         <div class="WarningBox">no filename found! back to <a href="index.php?page=pagesList&adminpage=1">Pages-Administration</a></div>
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
