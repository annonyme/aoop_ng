<?php
namespace core\net\rest;

class XWRESTMethodsCache{
	
	private $id="";
	private $methods=null;
	
	public function __construct(){
		
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id=$id;
	}
	
	/**
	 * @return XWRESTMethod
	 */
	public function getMethods(){
		return $this->methods;
	}
	
	public function setMethods($methods){
		$this->methods=$methods;
	}
}
