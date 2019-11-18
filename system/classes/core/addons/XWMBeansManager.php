<?php
/*
 * Created on 29.09.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2016/2018 Hannes Pries <http://www.hannespries.de>
  * Permission is hereby granted, free of charge, to any person obtaining a 
  * copy of this software and associated documentation files (the "Software"), 
  * to deal in the Software without restriction, including without limitation 
  * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
  * and/or sell copies of the Software, and to permit persons to whom the 
  * Software is furnished to do so, subject to the following conditions:
  * 
  * The above copyright notice and this permission notice shall be included in 
  * all copies or substantial portions of the Software.
  * 
  * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
  * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
  * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
  * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
  * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
  * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
  * IN THE SOFTWARE.
  */

namespace core\addons;

use core\utils\XWServerInstanceToolKit;
use DOMDocument;
use DirectoryIterator;
use ReflectionClass;
 
class XWMBeansManager{
    private $addonInstances=[];
	
	public function __construct(){
		
	}
	
	private function readInConfigParamsFromFile($filename){
		$params=[];
		if(is_file($filename)){
			$doc = new DOMDocument(); 
			$doc->load($filename);
			$config=$doc->getElementsByTagName("config");
			if($config->item(0)->hasChildNodes()){
				$nodes=$config->item(0)->childNodes;
				$node=null;
				foreach ($nodes as $node){
   					$key=$node->nodeName;
   					$value=$node->nodeValue;
   					if($key!="#text"){
   						//echo "debug: ".$key." -- ".$value." \n";   								
   						$params[$key]=$value;  									
   					}  								
				}	
			}
		}
		return $params;
	}
	
	private function hasClassMethod($class,$method){
		$methods=get_class_methods($class);
		$found=false;
		$methodCount=count($methods);
		for($i=0;$i<$methodCount;$i++){
			if($methods[$i]==$method){
				$found=true;
			}
		}
		return $found;
	}
	
	private function generateGetterSetterMethodName($attr,$type="get"){
		$func = function ($a) {
			return strtoupper($a[2]);
		};
		$attr=preg_replace_callback("/(_|^)(\w)/", $func,$attr);
    	return $type.$attr;
	}
	
	public function createMBeansArrayByPath($path="addons"){
        $addonInstances=[];       
        $di = new DirectoryIterator($path);
        foreach($di as $file){
            if($file->isDir()){
                $filename=$file->getFilename();
            	if(file_exists($path.$filename."/".$filename.".php")){
                    include_once($path.$filename."/".$filename.".php");
                    $addonInstances[$filename]=new $filename;
                    
                    if(file_exists($path.$filename."/config.xml")){
                    	$doc = new DOMDocument(); 
						$doc->load($path.$filename."/config.xml");
						$config=$doc->getElementsByTagName("config");
						if($config->item(0)->hasChildNodes()){
							$nodes=$config->item(0)->childNodes;
							$node=null;
							foreach ($nodes as $node){
   								$key=$node->nodeName;
   								$val=$node->nodeValue;
   								if($key!="#text"){
   									//echo "debug: ".$key." -- ".$val." \n";   								
   									$methodName=$this->generateGetterSetterMethodName($key,"set");
                    				if($this->hasClassMethod($addonInstances[$filename], $methodName)){
                    					$addonInstances[$filename]->$methodName($val);
                    				}   									
   								}  								
							}	
						}
                    }
                }
            }
        }
        return $addonInstances; //array mit instancen aller addons...
	}
	
	/**
	 * Für nicht aoop-addons und so
	 */
	public function createMBeanByPath($path="",$className="",$descriptorFileName="",$checkForStartup=false){
		$object=null;
		if(is_file($path."/".$className.".php")){
			$params=$this->readInConfigParamsFromFile($descriptorFileName);
			
			if(!$checkForStartup || (isset($params["_startup"]) && $params["_startup"]=="true")){
				include_once($path."/".$className.".php");
				$clazz=new ReflectionClass($className);
				$object=$clazz->newInstance();
				
				//set template path if exists and it is a template rendering addon
				if($object instanceof XWRenderingAddon && is_dir($path . "/templates/")){
				    $object->setTemplatePath($path . "/templates/");
				}
                //set override template path if exists and it is a template rendering addon
                if($object instanceof XWRenderingAddon &&
					is_dir(XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'addons/' . $className . "/")){
                    $object->setOverrideInstancePath(XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'addons/' . $className . "/");
                }
				
				//properties from config file
				while (list ($key, $val) = each($params)) {
                	$methodName=$this->generateGetterSetterMethodName($key,"set");
                	if($this->hasClassMethod($object, $methodName)){
                    	$object->$methodName($val);
                	}
            	}
			}				
		}
		return $object;
	}     
} 
?>
