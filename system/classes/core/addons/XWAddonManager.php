<?php
/*
 * Created on 01.09.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2010/2014/2016 Hannes Pries <http://www.hannespries.de>
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

use core\logging\XWLoggerFactory;
use core\modules\addons\ModuleAddonManager;
use core\utils\XWServerInstanceToolKit;
use core\utils\XWLocalePropertiesReader;
use core\utils\config\GlobalConfig;
use Exception;
use ReflectionException;

class XWAddonManager{
	
	private $addons=[];
	private $path="";
	
	static private $instance=null;
	
	/**
	 * @return XWAddonManager
	 */
	static public function instance():XWAddonManager {
		if(self::$instance==null){
			self::$instance=new self();			
			self::$instance->loadAddonsByPathForStartup(GlobalConfig::instance()->getValue("addonfolder"));
		}
		return self::$instance;
	}
	
	public function __construct(){
		$this->path=GlobalConfig::instance()->getValue("addonfolder");
	}

    /**
     * @param string $path
     *
     * @throws ReflectionException
     */
	public function loadAddonsByPathForStartup(string $path = "../addons"){
		$files=[];
		if(!isset($_SESSION["XW_ADDON_FOLDERLIST"])){
			$files=scandir($path);
			$_SESSION["XW_ADDON_FOLDERLIST"]=$files;
		}
		else{
			$files=$_SESSION["XW_ADDON_FOLDERLIST"];
		}
        
        $mbeans=new XWMBeansManager();
        $fileCount=count($files);
        $itk=XWServerInstanceToolKit::instance();
        for($i=0;$i<$fileCount;$i++){
        	if(!isset($this->addons[$files[$i]])){
        		$fileName=$files[$i];
        		$config="";				
				if(is_file($itk->getCurrentInstanceDeploymentRootPath().$fileName."-config.xml")){
					$config=$itk->getCurrentInstanceDeploymentRootPath().$fileName."-config.xml";				
				}
				else{
					$config=$path.$fileName."/config.xml";
				}
				
				$addon=$mbeans->createMBeanByPath($path."/".$fileName,$fileName,$config,true);
        		if($addon!=null){
        			if(method_exists($addon,"setAddonManager")){
	        			$addon->setAddonManager($this);
	        		}
        			$this->addons[$fileName]=$addon;
        		}
        	}        	
        }
	}

    /**
     * @param string $name
     *
     * @return mixed|null
     * @throws ReflectionException
     */
	public function getAddonByName(string $name){		
		//TODO check path exists.. before search for config file
		if(!isset($this->addons[$name])){
			$mbeans=new XWMBeansManager();
			$config=$this->path.$name."/config.xml";
			$itk=XWServerInstanceToolKit::instance();
			if(file_exists($itk->getCurrentInstanceDeploymentRootPath().$name."-config.xml")){
				$config=$itk->getCurrentInstanceDeploymentRootPath().$name."-config.xml";				
			}
			
			$addon=$mbeans->createMBeanByPath($this->path."/".$name, $name, $config, false);
        	if($addon!=null){
        		if(method_exists($addon,"setAddonManager")){
        			$addon->setAddonManager($this);
        		}
                if(method_exists($addon,"setPath")){
                    $addon->setPath($this->path."/".$name);
                }
                if(method_exists($addon, 'setLogger')){
        		    $addon->setLogger(XWLoggerFactory::getLogger(get_class($addon)));
                }
        		$this->addons[$name]=$addon;
        	}
		}
		else {
		    $addon = ModuleAddonManager::instance()->getAddon($name);
		    if($addon){
		        $this->addons[$name] = $addon;
            }
        }
		
		if(isset($this->addons[$name])){
			return $this->addons[$name];
		}
		else{
			return null;
		}
	}

    /**
     * @param $name
     *
     * @return mixed|null
     * @throws ReflectionException
     */
	public function get($name){
	    return $this->getAddonByName($name);
    }

    public function set($name, $addon){
        if($addon!=null){
            if(method_exists($addon,"setAddonManager")){
                $addon->setAddonManager($this);
            }
            if(method_exists($addon,"setPath")){
                $addon->setPath($this->path."/".$name);
            }
            if(method_exists($addon, 'setLogger')){
                $addon->setLogger(XWLoggerFactory::getLogger(get_class($addon)));
            }
            $this->addons[$name]=$addon;
        }
    }

    //TODO check for module addons
	public function getAddonPath($name = ''){
	    $result = null;
	    try{
            if(!isset($this->addons[$name])){
                $result = $this->path."/".$name;
            }
        }
        catch(Exception $e){

        }
        return $result;
    }
} 
