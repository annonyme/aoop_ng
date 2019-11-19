<?php
namespace core\logging;

interface XWAppender{
	/**
	 * @param string $msg
	 * @param integer $timestamp
	 * @param array $appenderConfig
	 */
	public function write($msg, ?\Exception $exception, $type, array $appenderConfig);
}