<?php
namespace core\logging;

use Exception;

interface XWAppender{
    /**
     * @param $msg
     * @param Exception|null $exception
     * @param $type
     * @param array $appenderConfig
     *
     * @return mixed
     */
	public function write($msg, ?Exception $exception, $type, array $appenderConfig);
}