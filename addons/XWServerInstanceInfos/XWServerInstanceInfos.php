<?php
/*
 * Created on 29.08.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2012/2013 Hannes Pries <http://www.annonyme.de>
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

use core\utils\XWServerInstanceToolKit;
use core\utils\config\DoopSysEnvVarsXML;
use core\utils\config\GlobalConfig;
 
class XWServerInstanceInfos{
	
	private $xmlReader=null;
	private $baseURL="";
	private $parseURL="";
	private $cache=null;
	private $instanceName="";
	
	public function __construct(){
		$this->cache=[];
		
		$itk=XWServerInstanceToolKit::instance();		
		$this->instanceName=$itk->getCurrentInstanceName();
		
		$deployerPath=GlobalConfig::instance()->getValue("instancesfolder").$this->instanceName."/deploy.xml";
		$xmlReader=new DoopSysEnvVarsXML($deployerPath);
		$this->xmlReader=$xmlReader;		
	}
	
	/**
	 * @deprecated
	 */
	public function getInfo($name){
		return $this->getInfoByName($name);
	}
	
	public function getInfoByName($name){		
		if($this->existsInfo($name)){
			return $this->cache[$name];
		}
		else{
			return "";
		}		
	}
	
	public function saveInfo($key,$value){
		$instanceToolKit=XWServerInstanceToolKit::instance();
		$switch=$instanceToolKit->getServerSwitch();
		$switch->saveSingleValueToDescriptor($key,$value);
		$this->cache[$key]=$value;
	}
	
	public function existsInfo($name){
		if(isset($this->cache[$name])){
			return true;
		}
		else{
			$xmlReader=$this->xmlReader;
			if($xmlReader->existsEnvVar($name)){
				$this->cache[$name]=trim($xmlReader->getEnvVar($name));
				return true;
			}	
		}
		return false;			
	}
	
	public function printInfoByName($name){
		echo $this->getInfoByName($name);
	}
	
	public function getInstanceName(){
		return $this->instanceName;
	}
	
	public function setInstanceName($instanceName){
		$this->instanceName=$instanceName;
	}
} 
