<?php
namespace core\datastorage;

use DOMDocument;
use core\utils\config\GlobalConfig;
use Exception;

/*
 * Created on 25.10.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 /*
  * Copyright (c) 2007/2009/2015 Hannes Pries <http://www.annonyme.de>
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
  
class XWDataStorageFactory{
	private $configFilePath="";
	static private $storeList=[];

    /**
     * XWDataStorageFactory constructor.
     * @param string $configFilePath
     * @throws Exception
     */
	public function __construct($configFilePath=''){
		if(!is_file($configFilePath)){
			throw new Exception("datastore config-file not found: " . $configFilePath);
		}
		$this->configFilePath=$configFilePath;
		if($this->getSize()==0){
			$this->loadStores($configFilePath);
		}		
	}
	
	public function addDataStore($dataStore){
		self::$storeList[count(self::$storeList)]=$dataStore;
	}
	
	public function getSize(){
		return count(self::$storeList);
	}
	
	public function getDataStore($index){
		return self::$storeList[$index];
	}
	
	public function getDataStoreByName($name){
		$store=new XWDataStore();
		$listedStore=null;
		$size=$this->getSize();
		
		$name=strtoupper($name);
		$found=false;
		for($i=0;$i<$size && !$found;$i++){
			$listedStore=$this->getDataStore($i);
			if($listedStore->getName()==$name){
				$store=$listedStore;
				$found=true;
			}
		}
		return $store;
	}
	
	public function existsDataStoreInByName($name){
		return $this->getDataStoreByName($name)->getName()!='';
	}
	
	//---
	
	private function loadStores($configFilePath=''){
		if($configFilePath!=""){
			$this->configFilePath=$configFilePath;
		}
		
		if(isset($_SESSION["XW_STORELIST_".$this->configFilePath]) && GlobalConfig::instance()->isDevMode()){
			self::$storeList=$_SESSION["XW_STORELIST_".$this->configFilePath];
		}			
		else if(file_exists($this->configFilePath)){
		    if(preg_match("/\.xml$/",$this->configFilePath)){
		    	$doc = new DOMDocument();
		    	$doc->load($this->configFilePath);
		    	$stores=$doc->getElementsByTagName("store");
		    	foreach($stores as $store){
		    		$attrs=$store->attributes;
		    		$ds=new XWDataStore();
		    		foreach($attrs as $attr){
		    			if($attr->name=="name"){
		    				$ds->setName(strtoupper($attr->value));
		    			}
		    			if($attr->name=="type"){
		    				$ds->setType($attr->value);
		    			}
		    		}
		    	
		    		//if($name==null || $ds->getName()==strtoupper($name) || $name==""){
		    			$children=$store->childNodes;
		    			foreach($children as $child){
		    				if(strtolower($child->nodeName)=="savepath"){
		    					$ds->setSavePath($child->nodeValue);
		    				}
		    				else if(strtolower($child->nodeName)=="loadfullpath"){
		    					$ds->setLoadFullPath($child->nodeValue);
		    				}
		    				else if(strtolower($child->nodeName)=="loadrelativepath"){
		    					$ds->setLoadRelativePath($child->nodeValue);
		    				}
		    				else if(strtolower($child->nodeName)=="host"){
		    					$ds->setHost($child->nodeValue);
		    				}
		    				else if(strtolower($child->nodeName)=="user"){
		    					$ds->setUser($child->nodeValue);
		    				}
		    				else if(strtolower($child->nodeName)=="password"){
		    					$ds->setPassword($child->nodeValue);
		    				}
		    			}
		    			$this->addDataStore($ds);
		    		//}
		    	}
		    }
		    else{
		    	//load json		    	
		    	$json=json_decode(file_get_contents($this->configFilePath),true);
		    	$storesCount=count($json["stores"]);
		    	for($i=0;$i<$storesCount;$i++){
		    		$store=$json["stores"][$i];
		    		$ds=new XWDataStore();
		    		
		    		try{
		    			$ds->setName(strtoupper($store["name"]));
		    			$ds->setType(strtoupper($store["type"]));
		    			
		    			$ds->setSavePath(strtoupper($store["savepath"]));
		    			$ds->setLoadFullPath(strtoupper($store["loadfullpath"]));
		    			$ds->setLoadRelativePath(strtoupper($store["loadrelativepath"]));
		    			if(isset($store["host"])){
		    				$ds->setHost(strtoupper($store["host"]));
		    				$ds->setUser(strtoupper($store["user"]));
		    				$ds->setPassword(strtoupper($store["password"]));
		    			}		    			
		    		}
		    		catch(Exception $e){
		    			
		    		}
		    	}
		    }			            
            $_SESSION["XW_STORELIST_".$this->configFilePath]=self::$storeList;
		}		
	}
} 
