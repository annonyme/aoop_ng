<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\pages\plain\XWPage;
use core\utils\XWServerInstanceToolKit;

$request = XWRequest::instance()->getRequestAsArray();
if(($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){ 
    $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";

	file_put_contents($pageDir.$request["filename"].".part",file_get_contents($_FILES["upfile"]["tmp_name"]),FILE_APPEND);
	if(isset($request["lastChunk"]) && $request["lastChunk"]=="true"){
		//create realname from filename split _ and ignore first part
		$parts=preg_split("/_/",$request["filename"]);
		$realName="";
		$partName=$request["filename"].".part";
		for($i=0;$i<count($parts);$i++){
			if($i>0){
				$realName.=$parts[$i];
				if($i<count($parts)-1){
					$realName.="_";
				}
			}
		}
		
		if(preg_match("/(template\.html)|(page\.json)$/i",$realName)){
			if(file_exists($pageDir.$realName)){
				//TODO create backup_copy
				unlink($pageDir.$realName);
			}
			rename($pageDir.$partName,$pageDir.$realName);
		}
		else if(preg_match("/\.html$/i",$realName)){
			if(file_exists($pageDir.$realName)){
				$pageCallName=preg_replace("/.html$/i","",$realName);
			    
			    $locale="";
			    if(isset($request["locale"])){
			     	$locale=$request["locale"];
			    }     
			    
			    $page=new XWPage();
			    $page->load($pageCallName,$pageDir,$locale);
			    $page->setContent(file_get_contents($pageDir.$partName));
			    $page->save();
			    
			    unlink($pageDir.$partName);
			}
			else{
				rename($pageDir.$partName,$pageDir.$realName);
				
				if(!file_exists($pageDir.preg_replace("/html$/i","xml",$realName))){
					$xml="<page>\n".
						 "  <name>".preg_replace("/\.html$/i","",$realName)."</name>\n".
						 "  <hidden>false</hidden>\n".
						 "  <parent></parent>\n".
						 "  <contentType>plain</contentType>\n".
						 "  <userId>".$_SESSION["XWUSER"]->getId()."</userId>\n".
						 "  <draft>false</draft>\n".
						 "  <lastEdit>2015/01/01 01:01:01</lastEdit>\n".
						 "</page>";
						 
					file_put_contents($pageDir.preg_replace("/html$/i","xml",$realName),$xml);
				}			
			}
		}
		//if html
			//check if file allready exists
				//load an replace content.. and save
			
			//rename and create sidecar-xml file
		else if(preg_match("/\.xml$/i",$realName)){
			if(file_exists($pageDir.$realName)){
				unlink($pageDir.$realName);
			}
			rename($pageDir.$partName,$pageDir.$realName);
		}	
		//if xml
			//simple replace	
		
	} 	
}
?>
