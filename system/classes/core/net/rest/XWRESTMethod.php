<?php
/*
 * Created on 14.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;

use core\utils\XWArrayList;
use Exception;
 
class XWRESTMethod{
	
	private $pattern="";
	private $callObj=null;
	private $methodName="";
	private $argumentList=null;
	private $format="json";
	
	public function __construct(){
		$this->argumentList=new XWArrayList();
	}
	
	public function check($url){
		$result = false;
		try{
			$result=preg_match($this->pattern,$url);
		}
		catch(Exception $e){
			
		}
		return $result;
	}
	
	public function call($url,$request,$callObj=null){
		$result=null;
		if($this->check($url)){
			$values=[];
			for($i=0;$i<$this->argumentList->size();$i++){
				$arg=$this->argumentList->get($i);
				$values[count($values)]=$this->resolveArgument($arg,$url,$request);
			}
			
			if($callObj!=null){
				$obj=$callObj;
			}
			else{
				$obj=$this->callObj;
			}
			
			$result=call_user_func_array(array($obj,$this->methodName),$values);
		}
		return $result;
	}
	
	private function resolveArgument(XWRESTArgument $arg,$url,$request){
		$result=null;
		$checkVarType=false;
		if($arg->getType()=="request"){
			$result=$request;
		}
		else if($arg->getType()=="session"){
			$result=$_SESSION;
		}
		else if($arg->isRequestValue()){
			$result=$request[$arg->getPattern()];
			$checkVarType=true;
		}
		else{
			$result=preg_replace($arg->getPattern(),"$".$arg->getGroup()."",$url);
			$checkVarType=true;
		}
		
		if($checkVarType){
			if($arg->getType()=="int"){
				$result=intval($result);
			}
			else if($arg->getType()=="bool" || $arg->getType()=="boolean"){
				$result=strtolower($result)=="true" || intval($result)==1;
			}
			else if($arg->getType()=="float"){
				$result=floatval($result);
			}
			if($arg->getType()=="int_abs"){
				$result=abs(intval($result));
			}						
			else if($arg->getType()=="protected_string"){
				$result=htmlspecialchars($result);
			}
			else if($arg->getType()=="base64_string"){
				$result=base64_decode($result);
			}
			else if($arg->getType()=="json_encoded_string"){
				$result=urldecode($result);
			}
		}	
		
		return $result;
	}
	
	public function getPattern(){
		return $this->pattern;
	}
	
	public function setPattern($pattern){
		$this->pattern=$pattern;
	}
	
	public function getCallObj(){
		return $this->callObj;
	}
	
	public function setCallObj($callObj){
		$this->callObj=$callObj;
	}
	
	public function getMethodName(){
		return $this->methodName;
	}
	
	public function setMethodName($methodName){
		$this->methodName=$methodName;
	}
	
	public function getArgumentList(){
		return $this->argumentList;
	}
	
	public function setArgumentList($argumentList){
		$this->argumentList=$argumentList;
	}
	public function getFormat() {
		return $this->format;
	}
	public function setFormat($format) {
		$this->format = $format;
	}	
} 
