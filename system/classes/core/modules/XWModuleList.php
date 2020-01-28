<?php
/*
 * Created on 17.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2011/2014/2015/2016/2017/2018 Hannes Pries <http://www.hannespries.de>
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

namespace core\modules;

use core\utils\XWServerInstanceToolKit;
use core\utils\XWArrayList;
use DirectoryIterator;
use DOMDocument;
use core\utils\config\GlobalConfig;
use Exception;

class XWModuleList{
	
	private $list=null;	
	private $deloymentSubPath ="";
	
	public function __construct(){
		$this->list=new XWArrayList();
		$this->deloymentSubPath = GlobalConfig::instance()->getValue("moduledeployfolder");
		$this->deloymentSubPath .= GlobalConfig::instance()->getValue("moduledeployfile");
	}
	
	public function loadByCurrentInstance(string $pagePath=""){		
		if($pagePath==""){
		    $pageDir=XWServerInstanceToolKit::instance()->getServerSwitch()->getPages();
			$pagePath=$pageDir;
		}
		$this->load($pagePath);
	}
	
	public function load(string $path="", string $globalModulePath = ""){
		$this->list=new XWArrayList();
        $files = [];
		
		// only 5 for inner caching.. outer cachng of full list as objects uses 25 (full module lsit fresh in 5*25=125 list loads)
		if(isset($_SESSION["XW_MODULE_LIST_".$path]) && $_SESSION["XW_MODULE_LIST_REFRESH"]<5 && !isset($_REQUEST["clearClassPath"])){
			$files=$_SESSION["XW_MODULE_LIST_".$path];
			$_SESSION["XW_MODULE_LIST_REFRESH"]++;
		}
		else{
			$di=new DirectoryIterator($path);
			foreach($di as $file){
				//load by module-folder in current folder
			    if($file->isDir() && !$file->isDot() && file_exists($path.$file->getFilename()."/".$this->deloymentSubPath)){
			        $files[$file->getFilename()]=['path' => $path.$file->getFilename(), 'global' => strlen($globalModulePath) == 0];
				}
				//load by custom deploymentdescriptor with name of the module in {instance}/modules/{name}.xml
				//doesn't need content in the file <module></module> is enough
				else if(strlen($globalModulePath) > 0 && $file->isFile() && preg_match("/\.xml$/i", $file->getFilename())){
				    if(is_dir($globalModulePath. $file->getFilename())){
				        $files[$file->getFilename()] = ['path' => $globalModulePath. $file->getFilename(), 'global' => true];
				    }
				}
			}			
			$_SESSION["XW_MODULE_LIST_".$path]=$files;			
			$_SESSION["XW_MODULE_LIST_REFRESH"]=0;
		}

		//load by folder in current-folder
        foreach($files as $file){
		    $module=$this->createModuleByPath($file['path'], $file['global']);
            $this->addModule($module);
        }
	}
	
	private function createModuleByPath(string $path, bool $isGlobal = false): XWModule {
		$module=new XWModule();
		
		$parts=preg_split("/\//",$path);
		$dirName=$parts[count($parts)-1];
		$module->setInstance($isGlobal);
		
		$module->setCallName($dirName);
		$module->setPath($path);
		$module->setHasOwnStyle(is_file($path."/resources/ownstyle.css")); //TODO.. caching
		
		//module own deployment descriptor
		if(is_file($path."/".$this->deloymentSubPath)){
		    $this->readModuleDeploymantDescriptor($path."/".$this->deloymentSubPath, $module);                                  
		}	
		
		//custom instance override
        try{
            $customDescriptor=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath().'modules/'.$module->getCallName().".xml";
            if(is_file($customDescriptor)){
                $this->readModuleDeploymantDescriptor($customDescriptor, $module);
                $module->setCustomDescriptorPath($customDescriptor);
            }
        }
        catch(Exception $e){

        }
		
		if($module->getName()==""){
		    $module->setName(ucfirst($dirName));
		}
		
		return $module;
	}
	
	private function readModuleDeploymantDescriptor(string $path, XWModule $module){
	    if(is_file($path)){
	        $doc = new DOMDocument();
	        $doc->load($path);
	        $root=$doc->getElementsByTagName("module");
	        if($root->length>0 && $root->item(0)->hasChildNodes()){
	            $items=$root->item(0)->childNodes;
	            /** @var \DOMElement $item */
                foreach ($items as $item){
	                if($item->nodeName=="name"){
	                    $module->setName($item->nodeValue);
	                }
	                else if($item->nodeName=="hidden"){
	                    $module->setHidden(strtolower($item->nodeValue)=="true");
	                }
	                else if($item->nodeName=="dictionary"){
	                    $module->setDictionaryPath($module->getPath() . "/" . $item->nodeValue);
	                }
	                else if($item->nodeName=="version"){
	                    $module->setVersion($item->nodeValue);
	                }
	                else if($item->nodeName=="admingroup"){
	                    $module->setAdminGroup(trim($item->nodeValue));
	                }
	                else if($item->nodeName=="changefreq"){
	                    $module->setChangeFrequence(trim($item->nodeValue));
	                }
                    else if($item->nodeName=="required"){
                        $children = $item->childNodes;
                        /** @var \DOMNode $child */
                        foreach ($children as $child){
                            $req = [];
                            if(strtolower($child->nodeName) == 'plugin'){
                                $req[] = trim($child->nodeValue);
                            }
                            $module->setRequiered($req);
                        }
                    }
	            }
	            
	            $permissions=$doc->getElementsByTagName("permission");
	            if($permissions->length>0){
	                for($i=0;$i<$permissions->length;$i++){
	                    $module->addPermission($permissions->item($i)->nodeValue);
	                }
	            }

                /**
                 * @deprecated use mvc with noRendering-Flag
                 */
	            $nonTextPagesNodes=$doc->getElementsByTagName("nontextpage");
	            if($nonTextPagesNodes->length>0){
	                for($i=0;$i<$nonTextPagesNodes->length;$i++){
	                    $module->addNonTextPage($nonTextPagesNodes->item($i)->nodeValue);
	                }
	            }
	            /**
	             * @deprecated use rest.xml to define rest services TODO remove this
	             */
	            $jsonPagesNodes=$doc->getElementsByTagName("jsonrestpage");
	            if($jsonPagesNodes->length>0){
	                for($i=0;$i<$jsonPagesNodes->length;$i++){
	                    $module->addJsonPage($jsonPagesNodes->item($i)->nodeValue);
	                    $module->addNonTextPage($nonTextPagesNodes->item($i)->nodeValue);
	                }
	            }
	        } 
	    }
	}
	
	public function addModule(XWModule $module){
		$this->list->add($module);
	}
	
	public function getSize():int {
		return $this->list->size();
	}
	
	/**
	 * @param int $index
	 * @return XWModule
	 */
	public function getModule(int $index): XWModule{
		return $this->list->get($index);
	}
	
	public function exists(string $moduleCallName):bool {
		$module=null;
		$size=$this->getSize();
		for($i=0;$i<$size;$i++){
			$module=$this->getModule($i);
			if($module->getCallName()==$moduleCallName){
				return true;
			}
		}
		return false;
	}
	
	public function getModuleByCallName($moduleCallName){
		$module=null;
		$size=$this->getSize();
		for($i=0;$i<$size;$i++){
			$module=$this->getModule($i);
			if($module->getCallName()==$moduleCallName){
				return $module;
			}
		}
		return false;
	}
	
	public function clear(){
		$this->list=new XWArrayList();
	}
	
	public function sortByName(bool $desc=false){
		//TODO use uasort with own function

	    $array=[];
		$module=null;
		$size=$this->getSize();
		for($i=0;$i<$size;$i++){
			$module=$this->getModule($i);
			$array[$module->getName()]=$module;
		}
		
		if(!$desc){
			ksort($array);
		}
		else{
			krsort($array);
		}
		
		$this->clear();
		
		foreach ($array as $val) {
    		$this->addModule($val);
		}		
	}

    /**
     * removed modules with not fullfilled requirements
     * @param array $modules
     * @return array
     */
	public function requieredListCheck($modules = []){
	    $names = [];
	    /** @var XWModule $module */
        foreach($modules as $module){
	        $names[$module->getCallName()] = true;
        }

        $filtered = [];
        /** @var XWModule $module */
        foreach($modules as $module){
            $ok = true;
            foreach($module->getRequiered() as $req){
                if(!isset($names[$req])){
                    $ok = false;
                }
            }
            if($ok){
                $filtered[] = $module;
            }
        }

        if(count($modules) > count($filtered)){
            $modules = $this->requieredListCheck($filtered);
        }
        return $modules;
    }

	/**
	 * @return XWArrayList
	 */
	public function toArrayList(): XWArrayList{
	    return $this->list;
	}
}