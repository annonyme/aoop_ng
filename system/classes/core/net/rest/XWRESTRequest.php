<?php
/*
 * Created on 24.02.2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace core\net\rest;
 
class XWRESTRequest{
	
	public function __construct(){
		
	}
	
	public function create($url,$request){
		$result=[];
		$urlParts=preg_split("/\?/",$url);
		if(count($urlParts)>1){
			$parts=preg_split("/&/",$urlParts[1]);
			$cnt=count($parts);
			for($i=0;$i<$cnt;$i++){
				$part=$parts[$i];
				$values=preg_split("/=/",$part);
				if(count($values)==2){
					$result[$values[0]]=$values[1];
				}
				elseif(count($values)==1){
					$result[$values[0]]="";
				}				
			}
		}		
		$result=array_merge($request,$result);
		return $result;
	}
} 
