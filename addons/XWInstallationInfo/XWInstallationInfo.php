<?php
/*
 * Created on 13.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2012 Hannes Pries <http://www.annonyme.de>
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

use core\utils\config\DoopSysEnvVarsXML;
 
 class XWInstallationInfo{
 	 private $xmlReader=null;
 	 private $file="";
 	 private $cache=null;
 	 private $dontExistsCache=null;
 	 private $cacheCount=-1;
 	 
 	 public function __construct(){
 	 	$this->cache=[];
 	 	$this->dontExistsCache=[];
 	 }
 	 
 	 public function existsInfo($name){
 	 	if(!isset($this->cache[$name])){
 	 		if(preg_match("/xml$/i",$this->file)){
 	 			if($this->xmlReader==null){
 	 				$this->xmlReader=new DoopSysEnvVarsXML($this->file);
 	 			}
 	 			 
 	 			if(isset($this->cache[$name])){
 	 				return true;
 	 			}
 	 			else if(!isset($this->dontExistsCache[$name])){
 	 				$xmlReader=$this->xmlReader;
 	 				if($xmlReader->existsEnvVar($name)){
 	 					$this->cache[$name]=$xmlReader->getEnvVar($name);
 	 					return true;
 	 				}
 	 				else{
 	 					$this->dontExistsCache[$name]="-";
 	 				}
 	 			}
 	 			return false;
 	 		}
 	 		else{
 	 			if($this->cacheCount==-1){
 	 				$this->cache=json_decode(file_get_contents($this->file),true);
 	 				$this->cacheCount=count($this->cache);
 	 			}
 	 			return isset($this->cache[$name]);
 	 		}
 	 	}
 	 	return true;
 	 	
 	 }
 	 
 	 public function getInfoByName($name){
 	 	if($this->existsInfo($name)){
			return $this->cache[$name];
		}
		else{
			return "";
		}
 	 }
 	 
 	 public function printInfoByName($name){
 	 	 echo $this->getInfoByName($name);
 	 }
 	 
 	 public function setFile($file){
 	 	$this->file=$file;
 	 }
 	 
 	 public function getFile(){
 	 	return $this->file;
 	 }
 }
