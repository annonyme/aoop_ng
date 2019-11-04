<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 11.03.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$request = XWRequest::instance()->getRequestAsArray();

if(($_SESSION["XWUSER"]->isInGroup("admins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins")) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
    //$xml="<server>\n</server>";
    $toolKit=new XWServerInstanceToolKit();
    if(class_exists("XWServerInstanceToolKit")){    	
    	if(file_exists($toolKit->getCurrentInstanceDeploymentDescriptorPath()) && isset($request["deployXML"])){
    		//$xml=file_get_contents($toolKit->getCurrentInstanceDeploymentDescriptorPath());
    		file_put_contents($toolKit->getCurrentInstanceDeploymentDescriptorPath(),stripslashes($request["deployXML"]));    		
    		?>
			<div class="InfoBoxHeader">Current instance:</div>
			<div class="InfoBox">
  				<?=$toolKit->getCurrentInstanceDeploymentDescriptorPath() ?> saved.<a href="index.php?adminpage=1"> Back to Admin-Panel</a>.
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
