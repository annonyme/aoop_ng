<?php
/*
 * Created on 26.02.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 /*
  * Copyright (c) 2010/2011/2016 Hannes Pries <http://www.hannespries.de>
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
use DOMNode;
use ReflectionClass;
use Exception;
 
class XWAddon{
	
	private $name="";
	private $config="";
	private $configFileName="";
	private $path="";
	private $active=true;
	private $serverInstanceConfig=false;
	
	public function __construct(){
		
	}
	
	public function exists(string $name, string $addonsFolder):bool{
		return file_exists($addonsFolder."/".$name."/".$name.".php");
	}
	
	public function load(string $name, string $addonsFolder){
		if(file_exists($addonsFolder."/".$name."/".$name.".php")){
			$this->setName($name);
			$this->setPath($addonsFolder.$name."/");
			
			$configFilePath="";
			$itk=XWServerInstanceToolKit::instance();
					
			if(is_file($itk->getCurrentInstanceDeploymentRootPath().$this->name."-config.xml")){
				$configFilePath=$itk->getCurrentInstanceDeploymentRootPath().$this->name."-config.xml";
				$this->serverInstanceConfig=true;
			}
			else if(is_file($this->path."/config.xml")){
				$configFilePath=$this->path."/config.xml";
			}
			
			if($configFilePath!=""){
				$this->configFileName=$configFilePath;
				$this->setConfig(file_get_contents($this->configFileName));

				$doc = new DOMDocument(); 
				$doc->load($this->path."/config.xml");
				$actives=$doc->getElementsByTagName("active");
                /** @var DOMNode $active */
                foreach ($actives as $active){
                    if($active->nodeValue == "false"){
						$this->setActive(false);
					}
				}
			}
		}
	}
	
	public function save(){
		if(file_exists($this->configFileName)){
			file_put_contents($this->configFileName,$this->config);
		}
	}
	
	/**
	 * 
	 */
	public function instance(){
		$object=null;
		
		try{
			if($this->name!=""){
				require_once($this->path."/".$this->name.".php");
					
				$clazz=new ReflectionClass($this->name);
				$object=$clazz->newInstance();					
				$object->xwaddon=$this;
				
				//set template path if exists and it is a template rendering addon
				if($object instanceof XWRenderingAddon && is_dir($this->path . "/templates/")){
				    $object->setTemplatePath($this->path . "/templates/");
				}
					
				if($this->getConfig()!=""){
					$doc = new DOMDocument();
					$doc->loadXML($this->getConfig());
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
								if($this->hasClassMethod($object, $methodName)){
									$object->$methodName($val);
								}
							}
						}
					}
				}
			}
		}
		catch(Exception $e){
			//TODO logging 
		}		
		return $object;
	}

    /**
     * @param $class
     * @param $method
     *
     * @return bool
     */
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
	
	private function generateGetterSetterMethodName(string $attr, string $type="get"): string{
    	$attr=preg_replace_callback("/(_|^)(\w)/",create_function('$a','return strtoupper($a[2]);'),$attr);
    	return $type.$attr;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	public function getConfig(){
		return $this->config;
	}
	
	public function setConfig($config){
		$this->config=$config;
	}
	
	public function getPath(){
		return $this->path;
	}
	
	public function setPath($path){
		$this->path=$path;
	}
	
	public function isActive(){
		return $this->active;
	}
	
	public function setActive($active){
		$this->active=$active;
	}
	
	public function getConfigFileName(){
		return $this->configFileName;
	}
	
	public function setConfigFileName($configFileName){
		$this->configFileName=$configFileName;
	}
	
	public function isServerInstanceConfig(){
		return $this->serverInstanceConfig;
	}
	
	public function setServerInstanceConfig($serverInstanceConfig){
		$this->serverInstanceConfig=$serverInstanceConfig;
	}
} 
