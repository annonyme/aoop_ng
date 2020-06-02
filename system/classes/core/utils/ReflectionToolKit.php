<?php
namespace  core\utils;

use Exception;
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
     * @param $methodName
     * @param array $paramsValues
     * @param null $obj
     * @return mixed
     * @throws ReflectionException
     */
	public static function invokeMethodWithNamedParams($clazz, $methodName, $paramsValues = [], $obj = null) {
        $result = null;
        $ref = new ReflectionClass($clazz);
        if($ref->hasMethod($methodName)) {
            $method = $ref->getMethod($methodName);
            $params = $method->getParameters();
            $args = [];
            foreach ($params as $index => $param) {
                if(isset($paramsValues[$param->getName()])) {
                    $args[] = $paramsValues[$param->getName()];
                }
                else {
                    $args[] = null;
                }
            }
            $method->setAccessible(true);
            $result = $method->invokeArgs($obj, $args);
        }
        else {
            throw new Exception('method not found');
        }
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

    /**
     * @param string $clazz
     * @param string $methodName
     * @param string $argName
     * @param int $position
     * @return bool
     * @throws ReflectionException
     */
	public static function checkForArgument(string $clazz, string $methodName, string $argName, int $position = -1) {
	    $result = false;
	    $ref = new ReflectionClass($clazz);
	    if($ref->hasMethod($methodName)) {
	        $method = $ref->getMethod($methodName);
	        $params = $method->getParameters();
	        foreach ($params as $index => $param) {
	            if($param->getName() === $argName && ($position === -1 || $index === $position)) {
	                $result = true;
                }
            }
        }
	    return $result;
    }
}