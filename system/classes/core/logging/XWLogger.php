<?php
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

class XWLogger
{
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'info';
    const NOTICE = 'notice';
    const DEBUG = 'debug';

    private $clazz = '';
    private $appenders = [];
    private $levels = [];

    public function __construct($clazz, $appenders, $levels)
    {
        $this->clazz = $clazz;
        $this->appenders = $appenders;
        $this->levels = $levels;
    }

    /**
     *
     * @param string $type
     * @param string $msg
     * @param \Exception $e
     */
    public function log($type, $msg, \Exception $e = null)
    {
        foreach ($this->appenders as $key => $app) {
            XWLoggerFactory::write($msg, $e, $type, $app, $this->levels[$key]);
        }
    }

    public function error(\Exception $e)
    {
        $this->log(self::ERROR, $e->getMessage(), $e);
    }
}