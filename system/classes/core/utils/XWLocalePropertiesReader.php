<?php
/*
 * Created on 15.10.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2010/2014/2016 Hannes Pries <http://www.annonyme.de>
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

namespace core\utils;
 
class XWLocalePropertiesReader{
	
	private $entries=null;
	private $missingKeys=[];
	
	private $path="";
	private $locale="";
	private $useDefaultFileToComplete=false; 
	
	public function __construct(){
		$this->entries=[];
	}
	
	/**
	 * e.g. modules/xxx/dicts/lang_DE.prop
	 */
	public function importPropertiesBundle($path,$locale,$useDefaultFileToComplete=false){
		$this->path=$path;
		$this->locale=$locale;
		$this->useDefaultFileToComplete=$useDefaultFileToComplete;
		
		if(!isset($_SESSION["XW_TRANSLATIONS"])){
			$_SESSION["XW_TRANSLATIONS"]=[];
		}
		if(isset($_SESSION["XW_TRANSLATIONS"]["XW_TRANS_MODULE_".$path."_".$locale]) && !isset($_REQUEST["clearClassPath"])){
			$this->entries=$_SESSION["XW_TRANSLATIONS"]["XW_TRANS_MODULE_".$path."_".$locale];
		}
		else{
			$path=trim($path);
			$loadPath=preg_replace("/\.prop((erties)|(s))?$/i","",$path);
			$found=true;
			if(is_file($loadPath."_".$locale.".prop")){
				$loadPath=$loadPath."_".$locale.".prop";
			}
			else if(is_file($loadPath.".prop")){
				$loadPath=$loadPath.".prop";
			}
			else{
				$found=false;
			}
			
			if($found){
				$content=file_get_contents($loadPath);
				$lines=preg_split("/\n/i",$content);
				$lineCount=count($lines);
				for($i=0;$i<$lineCount;$i++){
					$line=$lines[$i];
					if(preg_match("/=/",$line)){
						$parts=preg_split("/=/i",$line);
						$this->entries[strtoupper(trim($parts[0]))]=trim($parts[1]);
					}
				}
			}
			
			if($useDefaultFileToComplete){
				$path=preg_replace("/\.prop(erties)?$/i","",$path);
				if($loadPath!=$path.".prop"  && is_file($path.".prop")){
					$loadPath=$path.".prop";
				}
				if(is_file($loadPath)){
					$content=file_get_contents($loadPath);
					$lines=preg_split("/\n/i",$content);
					$lineCount=count($lines);
					for($i=0;$i<$lineCount;$i++){
						if(preg_match("/=/",$lines[$i])){
							$parts=preg_split("/=/i",$lines[$i]);
							$key=strtoupper(trim($parts[0]));
							if(!isset($this->entries[$key])){
								$this->entries[$key]=trim($parts[1]);
							}
						}
					}
				}
			}
			$_SESSION["XW_TRANSLATIONS"]["XW_TRANS_MODULE_".$path."_".$locale]=$this->entries;
		}		
	}
	
	public function get($key){
		return $this->getEntry($key);
	}
	
	public function getEntry($key){
		$ukey=strtoupper($key);
		
		if(isset($_REQUEST["clearClassPath"])){
			$this->missingKeys=[];
		}
		
		if(isset($this->entries[$ukey])){
			return $this->entries[$ukey];
		}
		else{
			if(!isset($this->missingKeys[$key])){
				$this->importPropertiesBundle($this->path, $this->locale, $this->useDefaultFileToComplete);	
				if(isset($this->entries[$ukey])){
					return $this->entries[$ukey];
				}
				else{
					$this->missingKeys[$key]=1;
				}
			}			
			return $key;
		}
	}
	
	public function getEntryUpperCaseFirst($key){
		return ucfirst($this->getEntry($key));
	}

    /**
     * @param $key
     * @param null $inserts
     *
     * @return mixed|string|string[]|null
     */
	public function getEntryAndReplace($key,$inserts=null){
		$result=$this->getEntry($key);
		if($inserts!=null && is_array($inserts)){
			$cnt=count($inserts);
			for($i=0;$i<$cnt;$i++){
				$result=preg_replace("/\{\{".$i."\}\}/", $inserts[$i], $result);
			}
		}
		return $result;
	}
	
	/**
	 * @return array
	 */
	public function getAsArray(){
		$result = [];
		foreach($this->entries as $key => $value){
		    $result[strtolower($key)] = $value;
		    $result[$key] = $value;
		}	    
	    return $result;
	}
}
