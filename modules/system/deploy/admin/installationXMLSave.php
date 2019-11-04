<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;

/*
 * Created on 11.03.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray();

if($_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
    if(class_exists("XWServerInstanceToolKit")){    	
    	if(file_exists("system/config/info.xml") && isset($request["infoXML"])){
    		file_put_contents("system/config/info.xml",stripslashes($request["infoXML"]));    		
    		?>
			<div class="InfoBoxHeader">info.xml:</div>
			<div class="InfoBox">
  				info.xml saved.<a href="index.php?adminpage=1"> Back to Admin-Panel</a>.
			</div>
			<?php
    	}
    	else if(file_exists("system/config/info.json") && isset($request["infoXML"])){
    		file_put_contents("system/config/info.json",stripslashes($request["infoXML"]));
    		?>
    		<div class="InfoBoxHeader">info.json:</div>
    		<div class="InfoBox">
    			info.json saved.<a href="index.php?adminpage=1"> Back to Admin-Panel</a>.
    		</div>
    		<?php
    	}	
    	else{
    		?>
 	     	<div class="WarningBoxHeader">no xml</div>
         	<div class="WarningBox">no xml found!</div>
 	     	<?php
    	}
    }
    else{
    	?>
 	     <div class="WarningBoxHeader">no toolkit-class</div>
         <div class="WarningBox">no toolkit-class found!</div>
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
