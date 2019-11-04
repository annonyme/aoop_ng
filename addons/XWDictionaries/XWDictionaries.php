<?php
/*
 * Created on 15.10.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\utils\XWLocalePropertiesReader;
 
class XWDictionaries{
	
	private $dicts=null;
	private $emptyProps=null;
	
	public function __construct(){
		$this->dicts=[];
		$this->emptyProps=new XWLocalePropertiesReader();
	}
	
	public function addDictionary($name,$dict){
		$this->dicts[$name]=$dict;
	}
	
	public function getDictionary($name){
		if(isset($this->dicts[$name])){
			return $this->dicts[$name];
		}
		else{
			return $this->emptyProps;
		}
	}
	
	public function existsIn($name){
		return isset($this->dicts[$name]);
	}
} 
