<?php
/*
 * Created on 13.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;

use core\modules\factories\XWModuleListFactory;
 
class XWRESTServiceLoader{
	
	public function __construct(){		
	}
	
	/**
	 * return json
	 */
	public function process($url,$request){
		$result="";
		$req=new XWRESTRequest();
		$request=$req->create($url,$request);		
		
		$urlParts=preg_split("/\//",$url); // rest/{module}/{.. module specific ...}
		$count=count($urlParts);
		$moduleName="";
		
		$startPart=0;
		for($i=0;$i<$count && $moduleName=="";$i++){
			if($urlParts[$i]=="rest"){
				$moduleName=$urlParts[$i+1];
				$_REQUEST["XWRESTMODULE_NAME"]=$moduleName;
				$startPart=$i;
			}
		}
		//cut url (reconstruct url), so that it starts with "/rest/...." ("rest" is marked as startpart in loop above)
		$tempUrl="";
		for($i=$startPart;$i<$count;$i++){
			$tempUrl.="/".$urlParts[$i];
		}
		$url=$tempUrl;
		
		$modules=XWModuleListFactory::getFullModuleList();	
		if($modules->exists($moduleName)){
			$module=$modules->getModuleByCallName($moduleName);
			$serviceDescr=$module->getPath()."/deploy/rest.xml";
			if(is_file($serviceDescr)){
				$wrapper=new XWRESTServiceWrapper($serviceDescr);
				$result=$wrapper->call($url,$request);
			}
		}
		else{
			$result=$this->noServiceFoundError();
		}
		return $result;
	}
	
	private function noServiceFoundError(){
		return "{'error':'true','errormessage':'module is not existing!','errortype':'XWREST'}";
	}
} 
