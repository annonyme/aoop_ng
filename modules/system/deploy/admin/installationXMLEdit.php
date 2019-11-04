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

if($_SESSION["XWUSER"]->isInGroup("admins")){
    $xml="<doop>\n</doop>";
    $type="xml";
    if(class_exists("XWServerInstanceToolKit")){    	
    	if(file_exists("system/config/info.xml")){
    		$xml=file_get_contents("system/config/info.xml");
    	}
    	else{
    		$xml=file_get_contents("system/config/info.json");
    		$type="json";
    	}
    }

?>
<div class="ActionBoxHeader">Current installation:</div>
<div class="ActionBox">
<form method="post" action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=installationXMLSave">
<?php
  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  	?>
<table>
  <tr>
    <td class="dataTableTdLeft">info.<?=$type ?>:</td>
  </tr>
  <tr>  
    <td class="dataTableTdRight">
      <textarea cols="80" rows="25" name="infoXML"><?=$xml ?></textarea>
    </td>
  </tr>
  <tr>  
    <td class="dataTableTdRight">
      <input type="submit" class="submit" value="save"/>
    </td>
  </tr>    
</table>
</form>
</div>
<br/>
<?php
}
?>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>
