<?php
use core\addons\XWAddonManager;
use core\utils\XWServerInstanceToolKit;
use core\net\XWRequest;

/*
 * Created on 11.03.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();

if($_SESSION["XWUSER"]->isInGroup("admins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins")){
    $xml="<server>\n</server>";
    $toolKit=new XWServerInstanceToolKit();
    if(class_exists("XWServerInstanceToolKit")){    	
    	if(file_exists($toolKit->getCurrentInstanceDeploymentDescriptorPath())){
    		$xml=file_get_contents($toolKit->getCurrentInstanceDeploymentDescriptorPath());
    	}
    }

?>
<div class="ActionBoxHeader">Current instance '<?=XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->getInstanceName() ?>':</div>
<div class="ActionBox">
<form method="post" action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=instanceXMLSave">
<table>
  <tr>
    <td class="dataTableTdLeft">deploy.xml (<?=$toolKit->getCurrentInstanceDeploymentDescriptorPath() ?>):</td>
  </tr>
  <tr>  
    <td class="dataTableTdRight">
      <textarea cols="80" rows="25" name="deployXML"><?=$xml ?></textarea>
    </td>
  </tr>
  <tr>  
    <td class="dataTableTdRight">
      <input type="submit" class="submit" value="save"/>
    </td>
  </tr>    
</table>
</form>
<br/>
<strong style="color:red;">* 'admins' (Global-Admins) is always Admin-Group. For all instances.
Set to 'admins' if only Global-Admins should have the rights to edit this instance.
If an other group also should have admin rights, select this group.</strong>
</div>
<br/>
<?php
}
?>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>

