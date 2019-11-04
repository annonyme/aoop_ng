<?php
namespace core\router;

use core\router\XWURLRouterListener;

class XWURLGenericIncludeListener implements XWURLRouterListener{
	
	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::checkType()
	 */
	public function checkType($type) {
		return strtolower($type) == "include";
	}

	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::call()
	 */
	public function call($data, $args = array()) {
		$result = new XWURLResolveResult();
		try{
			$path = isset($data["path"]) ? $data["path"] : "";
			if(!preg_match("/\/$/", $path) && strlen($path) > 0){
				$path .= "/";
			}
			$filename = array_shift($args);
			
			$ext = "";
			if(isset($data["extension"])){
				$ext = $data["extension"];
				if(strlen($ext) > 1 && substr($ext, 0, 1) != "."){
					$ext = ".".$ext;
				}
			}
			
			include_once $path.$filename.$ext;
			
			$result->setContent(["success" => true]);
		}
		catch(\Exception $e){
			$result->setContent(["success" => false]);
		}
		return $result;
	}
}