<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\utils\config\GlobalConfig;
use core\utils\filesystem\XWSimpleFileReader;

/*
 * Created on 23.08.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins")){
 	 if(isset($request["fileName"])){
	    $file=preg_replace("/[\/\\\\]/", "", $request["fileName"]); 
	    $folder=preg_replace("/[\/\\\\\.:]/", "", $request["theme"]);
 	 	
 	 	if(isset($request["fileContent"]) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){	        
	        file_put_contents("themes/".$folder."/".$file, stripslashes($request["fileContent"]));
	        ?>
	        <div class="InfoBoxHeader">Save file:</div>
	        <div class="InfoBox">saved.</div>
	        <?php
	     }     
	     
	     $reader=new XWSimpleFileReader("themes/".$folder."/".$file);	     
	     ?>
	     <?php
	       if(isset($request["editor"]) && GlobalConfig::instance()->getValue('wysiwygEditor', false)){
	     ?>
               <script src="https://tinymce.cachefly.net/4.0/tinymce.min.js"></script>
         <script  type="text/javascript">
         tinyMCE.init({
             mode : "textareas",
             theme : "simple" 
         });
         </script>
         <?php
	       }
	     ?>
 	     <div class="ActionBoxHeader">Edit Theme-File [<?=$folder."/".$file ?>]:</div>
         <div class="ActionBox">
            <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=editTheme&adminpage=1&fileName=<?=$file ?>">
              <?php
	  			XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
	  		  ?>
              <textarea name="fileContent" cols="120" rows="25"><?=$reader->getContent() ?></textarea>
              <br/>
              <input type="submit" class="submit" value="save"/>
            </form>            
         </div>
         
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
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=themes&adminpage=1">Themes-Administration</a></div>