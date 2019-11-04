<?php
/*
 * Created on 26.05.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\modules\resources;

use core\modules\factories\XWModuleListFactory;
use core\utils\config\GlobalConfig;
use Exception;

class XWModuleResourceLoader{
	
	private $mList=null;
	
	public function __construct(){
		$this->mList=XWModuleListFactory::getFullModuleList();
	} 
	
	private function getResourcePath($moduleName,$resourceName){
		$result="";		
		try{
			$resourceName=preg_replace("/\.+/",".",$resourceName);
			$resourceName=preg_replace("/\//","_",$resourceName);
			$resourceName=preg_replace("/^http/i","_",$resourceName);
			$resourceName=preg_replace("/^ftp/i","_",$resourceName);
			$resourceName=preg_replace("/:/","_",$resourceName);
			
			$moduleName=preg_replace("/\.+/",".",$moduleName);
			$moduleName=preg_replace("/\//","_",$moduleName);
			$moduleName=preg_replace("/^http/i","_",$moduleName);
			$moduleName=preg_replace("/^ftp/i","_",$moduleName);
			$moduleName=preg_replace("/:/","_",$moduleName);				
			
			$module=$this->mList->getModuleByCallName($moduleName);
			if($module->getName()!=""){
				if(file_exists($module->getPath()."/".GlobalConfig::instance()->getValue("moduledeployfolder")."resources/".$resourceName)){
					$result=$module->getPath()."/".GlobalConfig::instance()->getValue("moduledeployfolder")."resources/".$resourceName;
				}	
			}			
		}
		catch(Exception $e){
			echo $e;
		}
		return $result;
	}
	
	public function directOutputOfResource($moduleName,$resourceName){
		ob_clean();
		if(preg_match("/\.js$/",$resourceName)){
			header("Content-Type: application/javascript");
		}
		else if(preg_match("/\.xml$/",$resourceName)){
			header("Content-Type: application/xml");
		}
		else if(preg_match("/\.webapp$/",$resourceName)){
			header("Content-Type: application/x-web-app-manifest+json");
		}
		else if(preg_match("/\.json$/",$resourceName)){
			header("Content-Type: application/json");
		}
		else if(preg_match("/\.pdf$/",$resourceName)){
			header("Content-Type: application/pdf");
		}
		else if(preg_match("/\.png$/",$resourceName)){
			header("Content-Type: image/png");
		}
		else if(preg_match("/\.jp(e)?g$/",$resourceName)){
			header("Content-Type: image/jpeg");
		}
		else if(preg_match("/\.css$/",$resourceName)){
			header("Content-Type: text/css");
		}
		else{
			header("Content-type: application/octet-stream");
		}
		
		header("Content-Disposition:filename=\"".$resourceName."\"");		
		readfile($this->getResourcePath($moduleName,$resourceName));
		
		exit(0);
	}	
}
