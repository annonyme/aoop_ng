<?php
namespace  core\utils;

use ReflectionClass;
use ReflectionException;

class ReflectionToolKit{
    /**
     * @param $clazz
     * @param $method
     * @param array $args
     * @param null $obj
     *
     * @return mixed|null
     * @throws ReflectionException
     */
    public static function invokeMethod($clazz, $method, $args = [], $obj = null){
		$result = null;
		$ref = new ReflectionClass($clazz);
		$method = $ref->getMethod($method);
		$method->setAccessible(true);
		$result = $method->invokeArgs($obj, $args);
		return $result;
	}

    /**
     * @param $clazz
     * @param array $args
     *
     * @return object
     * @throws ReflectionException
     */
	public static function newInstance($clazz, $args = []){
		$ref = new ReflectionClass($clazz);
		return $ref->newInstance($args);
	}
}