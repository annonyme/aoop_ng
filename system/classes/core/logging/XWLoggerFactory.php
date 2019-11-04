<?php

/*
 * {
	appenders:[
	            {
	                name:'',
	                filename:'',
	                dateformat:'',
	                .....
	            }
	            ],
	classes:[
	            {
	                class:'',
	                appenders:[
	                    {
	                        name:'',
	                        level:''
	                    }
	                ]
	            }
	        ]
	}
 */

/*
 * Copyright (c) 2016 Hannes Pries <http://www.annonyme.de>
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the 'Software'),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace core\logging;

use core\utils\config\GlobalConfig;

class XWLoggerFactory
{
    private static $config = [];
    private static $appenderCache = [];

    private static $buffer = [];
    private static $blockActive = false;
    private static $blockTriggered = false;
    private static $blockTriggerLevel = 'alert';
    private static $blockVerboseLevel = 'debug';

    private static $values = [
        'alert' => 6,
        'critical' => 5,
        'error' => 4,
        'warning' => 3,
        'info' => 2,
        'notice' => 1,
        'debug' => 0,
    ];

    /**
     * @return XWLogger
     * @param string $clazz
     */
    public static function getLogger($clazz)
    {
        $file = getenv('XW_LOGGER_CONFIGGILE');
        if (!$file && class_exists(GlobalConfig::class)) {
            $file = GlobalConfig::instance()->getValue('configspath') . 'log.json';
        }

        if (count(self::$config) == 0 && file_exists($file)) {
            self::$config = json_decode(file_get_contents($file), true);
            foreach (self::$config['appenders'] as $appender) {
                self::$appenderCache[$appender['name']] = $appender;
            }
        }

        $appenders = [];
        $levels = [];

        foreach (self::$config['classes'] as $cla) {
            if (preg_match('/^' . $cla['name'] . '/i', $clazz)) {
                foreach ($cla['appenders'] as $app) {
                    $appenders[] = self::$appenderCache[$app['name']];
                    $levels[] = $app['level'];
                }
            }
        }

        return new XWLogger($clazz, $appenders, $levels);
    }

    public static function startBlock($triggerLevel = 'alert', $verboseLevel = 'debug')
    {
        self::$blockActive = true;
        self::$buffer = [];
        self::$blockTriggered = false;
        self::$blockTriggerLevel = $triggerLevel;
        self::$blockVerboseLevel = $verboseLevel;
    }

    public static function endBlock()
    {
        if (self::$blockActive) {
            foreach (self::$buffer as $item) {
                if (self::$blockTriggered || $item[2] >= self::$blockVerboseLevel) {
                    self::writeRaw($item[0], $item[1], $item[2], $item[3]);
                }
            }

            self::$buffer = [];
            self::$blockTriggered = false;
            self::$blockTriggerLevel = 'alert';
            self::$blockVerboseLevel = 'debug';
        }
    }

    public static function write($msg, $exception, $type, $appenderConfig, $verboseLevel = 'debug')
    {
        if (self::$blockActive) {
            if (self::$values[self::$blockTriggerLevel] >= self::$values[strtolower($type)]) {
                self::$blockTriggered = true;
            }
            self::$buffer[] = [$msg, $exception, $type, $appenderConfig, $verboseLevel];
        } else if (self::$values[strtolower($verboseLevel)] <= self::$values[strtolower($type)]) {
            self::writeRaw($msg, $exception, $type, $appenderConfig);
        }
    }

    private static function writeRaw($msg, $exception, $type, $appenderConfig)
    {
        if (!isset($appenderConfig['output']) || $appenderConfig['output'] == 'file') {
            $appender = new XWFileAppender();
            $appender->write($msg, $exception, $type, $appenderConfig);
        }
    }
}