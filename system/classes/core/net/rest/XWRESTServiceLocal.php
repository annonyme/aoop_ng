<?php
namespace core\net\rest;

class XWRESTServiceLocal{
	public function __construct(){
		
	}
	
	public static function call($url,$request){
		$loader=new XWRESTServiceLoader();
		return $loader->process($url,$request);
	}
}
