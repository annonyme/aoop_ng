<?php

namespace core\router;

interface XWURLRouterListener{
	/**
	 * @return bool
	 * @param string $type
	 */
	public function checkType($type);
	
	/**
	 * @return XWURLResolveResult
	 * @param array $data
	 * @param array $args
	 */
	public function call($data, $args = []);
}