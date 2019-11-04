<?php
namespace  core\utils;

class ReflectionToolKit{
	public static function invokeMethod($clazz, $method, $args = [], $obj = null){
		$result = null;
		$ref = new \ReflectionClass($clazz);
		$method = $ref->getMethod($method);
		$method->setAccessible(true);
		$result = $method->invokeArgs($obj, $args);
		return $result;
	}
	
	public static function newInstance($clazz, $args = []){
		$ref = new \ReflectionClass($clazz);
		return $ref->newInstance($args);
	}
}