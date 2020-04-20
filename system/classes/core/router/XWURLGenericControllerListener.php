<?php
namespace core\router;

use Exception;
use ReflectionClass;

/**
 * class [full qualified classname]
 * method [method-name]
 * singleton [true/false]
 * @author annonyme
 *
 */
class XWURLGenericControllerListener implements XWURLRouterListener{
	
	private static $singletons = [];
	
	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::checkType()
	 */
	public function checkType($type) {
		return strtolower($type) == "controller";
	}

	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::call()
	 */
	public function call($data, $args = []) {
		$result = new XWURLResolveResult();
		try{
			$clazz = $data["class"];
			$method = $data["method"];
			
			$singleton = strtolower($data["singleton"]) == "true";
			
			$obj = null;
			$ref = new ReflectionClass($clazz);
			if($singleton){
				if(isset(self::$singletons[$clazz])){					
					$obj = $ref->newInstance();
					self::$singletons[$clazz] = $obj;
				}
				else{
					$obj = self::$singletons[$clazz];
				}
			}
			else{
				$obj = $ref->newInstance();
			}
			
			$content = $ref->getMethod($method)->invokeArgs($obj, $args);
			
			$result->setContent($content);
			$result->setType(get_class($content));
		}
		catch(Exception $e){
			$result->setException($e);
		}
		return $result;
	}

}