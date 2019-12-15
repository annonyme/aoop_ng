<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\menu\admin\XWAdminMenuBuilder;

/*
 * Created on 27.05.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$request = XWRequest::instance()->getRequestAsArray();
?>
<div class="panel panel-default">
<div class="panel-heading">
	Core-System (current instance '<?=XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->getInstanceName() ?>')
</div>
<div class="panel-body">
  aoop <?=XWAddonManager::instance()->getAddonByName("XWInstallationInfo")->getInfoByName("version") ?> <?=XWAddonManager::instance()->getAddonByName("XWInstallationInfo")->getInfoByName("versionstatus") ?> [<?=XWAddonManager::instance()->getAddonByName("XWInstallationInfo")->getInfoByName("builddate") ?>] - PHP: <?=phpversion() ?>
</div>
<div class="panel-body" id="systemAdminPanel">
	<?php
	  if($_SESSION["XWUSER"]->isInGroup("admins")){
	  	?>
	  	<a href="index.php?page=system&sub=phpInfo&adminpage=1">PHP-Info</a><br/>
	  	<a href="index.php?page=system&sub=installationXMLEdit&adminpage=1" class="hidden">Aoop-Installation-Administration</a><br/>
	  	<a href="index.php?page=system&sub=instance&adminpage=1">Server-Instance</a> [<a href="index.php?page=instanceXMLEdit&adminpage=1">XML</a>]<br/>
	  	<br/>
		<a href="index.php?page=system&sub=moduleList&adminpage=1">Modules</a><br/>
	  	<?php
	  }
	?>
	<a href="index.php?page=system&sub=dataStores&adminpage=1">DataStores</a><br/>
</div>
</div>
<?php
	 if($_SESSION["XWUSER"]->isInGroup("admins")){
?>
<?php
	 }
?>

<?php
  if($_SESSION["XWUSER"]->isInGroup("admins")){
  	?>
<div class="panel panel-default">
  	<div class="panel-heading">
		User-Administration
    </div>
	<div class="panel-body">
  		<a href="index.php?page=system&sub=groups&adminpage=1">User-Groups</a><br/>
		<a href="index.php?page=system&sub=usersList&adminpage=1">Users</a><br/>
	</div>
</div>
  	<?php	
  }
?>
<div class="panel panel-default">
<div class="panel-heading">
	Content-Administration
</div>
<div class="panel-body">
    <a href="index.php?page=system&sub=pagesList&adminpage=1">Pages</a><br/>
    <a href="index.php?page=system&sub=pages&adminpage=1" class="hidden">Pages-File Browser</a><br/>
    <a href="index.php?page=system&sub=images&adminpage=1">Images</a><br/>
</div>
</div>

<div class="panel panel-default">
<div class="panel-heading">Modules</div>
<div class="panel-body">
<?php
    $mBuilder=new XWAdminMenuBuilder();
    $mBuilder->buildFromModules($_SESSION["XWUSER"]);
    for($i=0;$i<$mBuilder->getSize();$i++){
    	echo $mBuilder->getMenuFileContent($i)."<br/>";
    }
?>
</div>
</div>