<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;

/*
 * Created on 26.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray();
 if($_SESSION["XWUSER"]->isInGroup("admins")){
 	 if(isset($request["imageName"]) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
	     if(isset($request["newimageName"])){
	     	 $realPath=preg_replace("/___/i","/",$request["subdir"]);
  		 	 $realPath=preg_replace("/(\/{2)|(\.{2)/i","",$realPath);
  		 	 try{
  		 	 	rename("images/".$realPath.$request["imageName"],"images/".$realPath.$request["newimageName"]);
  		 	 }
  		 	 catch(Exception $e){
  		 	 	
  		 	 }
	     	 ?>
 	         <div class="PresentationBoxHeader">Rename Image:</div>
             <div class="PresentationBox">Image was renamed 
             (<strong>[<?=$request["imageName"] ?>] to [<?=$request["newimageName"] ?>]</strong>).
             </div>
 	         <?php
	     }
	     else{
	     ?>
 	     <div class="PresentationBoxHeader">rename Image:</div>
         <div class="PresentationBox">
           <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=renameImage&adminpage=1&imageName=<?=$request["imageName"] ?>&subdir=<?=$request["subdir"] ?>">
           <?php
		  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
		  	?>
	           <strong>new Name:</strong> <input type="text" name="newimageName" value="<?=$request["imageName"] ?>" required/><br/>
	           <input type="submit" class="submit" value="rename"/>
           </form>
         </div>
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
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=images&adminpage=1&showFolder=<?=$request["subdir"] ?>">Images-Administration</a></div>
