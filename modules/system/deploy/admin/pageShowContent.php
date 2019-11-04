<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\pages\plain\XWPage;
use core\utils\XWServerInstanceToolKit;

$request = XWRequest::instance()->getRequestAsArray();
//load as resource by page
if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){ 
    $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
 	if(isset($request["pageName"]) && isset($request["type"]) && $request["type"]=="page"){
	    $pageCallName=$request["pageName"];
	    
	    $locale="";
	    if(isset($request["locale"])){
	     	$locale=$request["locale"];
	    }     
	    
	    $page=new XWPage();
	    $page->load($pageCallName,$pageDir,$locale);	     
	   
	   
	    //TODO set response to text
	   	echo "<html><body><pre><code>".XWAddonManager::instance()->getAddonByName("XWParserToolKit")->disableHtml($page->getContent())."</code></pre></body></html>";
    }
    else if(isset($request["pageName"]) && isset($request["type"]) && $request["type"]=="sidecar"){
    	$pageCallName=preg_replace("/\.{2}/i","",$request["pageName"]);
    	$pageCallName=preg_replace("/\s/i","",$pageCallName);
    	$pageCallName=preg_replace("/[\/]/i","",$pageCallName);
    	$content=file_get_contents($pageDir.$pageCallName.".xml");
    	echo "<html><body><pre><code>".XWAddonManager::instance()->getAddonByName("XWParserToolKit")->disableHtml($content)."</code></pre></body></html>";
    }
} 
?>

