<?php
/*
 * Created on 14.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;

use DOMDocument;
use core\utils\XWArrayList;
 
class XWRESTServiceWrapper{
	
	private $name="";
	
	private $xmlDoc=null;
	/**
	 * @var XWRESTMethodsCache
	 */
	private $methods=null;
	private $callObj=null;
	
	private $disableCORS=false;
	
	//uses caching of parsed methods, detect changes in xml with md5-hash of xml content
	public function __construct($xml){		
		$doc = new DOMDocument();
		$doc->load($xml);
		$this->xmlDoc=$doc;
		$idKey=md5(file_get_contents($xml));
		if(!isset($_REQUEST["reset_cache"]) && isset($_SESSION["XW_REST_METHODS_CACHE"]) && $_SESSION["XW_REST_METHODS_CACHE"]->getId()==$idKey && $_SESSION["XW_REST_METHODS_CACHE"]->getMethods()!=null){
			$this->init(false);
			$this->methods=$_SESSION["XW_REST_METHODS_CACHE"]->getMethods();
		}
		else{
			$this->init(true);
			
			$cache=new XWRESTMethodsCache();
			$cache->setId(md5($xml));
			$cache->setMethods($this->methods);
			$_SESSION["XW_REST_METHODS_CACHE"]=$cache;
		}
	}
	
	private function init($readFullDescriptor=true){
		$this->methods=new XWArrayList();
		
		//read method and arguments from xml		
		$className="";
		
		$roots=$this->xmlDoc->getElementsByTagName("service");
		$items=$roots->item(0)->childNodes;
        foreach ($roots as $item){
        	$attrs=$item->attributes;
            foreach($attrs as $attr){
               	if($attr->name=="class"){
               		$className=$attr->value;
               	}
               	else if($attr->name=="name"){
               		$this->name=$attr->value;
               	}               	
               	else if($attr->name=="disablecoors"){
               		$this->disableCORS=strtolower($attr->value)=="true";
               	}
            }
        }
        if($className!=""){
        	$obj=new $className();
        	$this->callObj=$obj;
        	
        	if($readFullDescriptor){
	        	foreach ($items as $item){
		        	if($item->nodeName=="method"){
		        		$method=new XWRESTMethod();
		        		//$method->setCallObj($obj);
		        		
		        		$attrs=$item->attributes;
			            foreach($attrs as $attr){
			               	if($attr->name=="name"){
			               		$method->setMethodName($attr->value);
			               	}
			               	else if($attr->name=="pattern"){
			               		$method->setPattern($attr->value);
			               	}
			               	else if($attr->name=="format"){
			               		$method->setFormat($attr->value);
			               	}
			            }
			            
			            $children=$item->childNodes;
			            foreach ($children as $child){
			            	if($child->nodeName=="argument"){
			            		$arg=new XWRESTArgument();
			            		
			            		$cAttrs=$child->attributes;
					            foreach($cAttrs as $cAttr){
					               	if($cAttr->name=="pattern"){
					               		$arg->setPattern($cAttr->value);
					               	}
					               	else if($cAttr->name=="group"){
					               		$arg->setGroup(intval($cAttr->value));
					               	}
					               	else if($cAttr->name=="requestvalue"){
					               		$arg->setRequestValue($cAttr->value=="true");
					               	}
					               	else if($cAttr->name=="type"){
					               		$arg->setType($cAttr->value);
					               	}
					            }
					            
					            if($arg->getPattern()==""){
					            	$arg->setPattern($method->getPattern());
					            }
					            		            		
			            		$method->getArgumentList()->add($arg);
			            	}
			            }	        		
		        		
		        		if($obj!=null && $method->getMethodName()!=""){
		        			$this->methods->add($method);
		        		}
		        	}
	        	}
        	}
        }
	}
	
	public function call($url,$request){
		$result=null;
		for($i=0;$i<$this->methods->size() && $result==null;$i++){
			/**
			 * @var XWRESTMethod $method
			 */
			$method=$this->methods->get($i);
			if($method->check($url)){				
				if($method->getFormat()=="json"){
					header("Content-Type: application/json; charset=UTF-8");
					$result=$method->call($url,$request,$this->callObj);
					$result=json_encode($result);
				}
				else{
					$result=$method->call($url,$request,$this->callObj);
				}
			}
		}
		return $result;
	}
	
	public function isDisableCORS(){
		return $this->disableCORS;
	}
} 
?>