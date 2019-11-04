<?php 

namespace core\router;

/**
 * function [full qualified name of the function]
 * @author annonyme
 *
 */
class XWURLGenericFunctionListener implements XWURLRouterListener{
	
	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::checkType()
	 */
	public function checkType($type) {
		return strtolower($type) == "function";
	}

	/**
	 * {@inheritDoc}
	 * @see \core\router\XWURLRouterListener::call()
	 */
	public function call($data, $args = array()) {
		$result = new XWURLResolveResult();
		try{
			$func = $data["function"];			
			
			$content = call_user_func_array($func, $args);
			
			$result->setContent($content);
			$result->setType(get_class($content));
		}
		catch(\Exception $e){
			$result->setException($e);
		}
		return $result;
	}

}