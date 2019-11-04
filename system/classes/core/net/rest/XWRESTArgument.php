<?php
/*
 * Created on 14.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;
 
class XWRESTArgument{
	
	private $pattern="";
	private $group=1;
	private $requestValue=false;
	private $type="";	
	
	public function __construct(){
		
	}
	
	public function getPattern(){
		return $this->pattern;
	}
	
	public function setPattern($pattern){
		$this->pattern=$pattern;
	}
	
	public function getGroup(){
		return $this->group;
	}
	
	public function setGroup($group){
		$this->group=$group;
	}
	
	public function isRequestValue(){
		return $this->requestValue;
	}
	
	public function setRequestValue($requestValue){
		$this->requestValue=$requestValue;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function setType($type){
		$this->type=$type;
	}
} 
