<?php
/*
 * Created on 24.02.2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;
 
class XWRESTResponse{
	
	public $error=false;
	public $errorMessage="";
	public $newSecToken="";
	public $type="";
	public $singleResult=false;
	public $restMethodSuccess=true;
	public $timestamp=0;
	
	public $results=null;
	
	public function __construct(){
		
	}
} 
