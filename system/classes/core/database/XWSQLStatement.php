<?php

namespace core\database;

/*
 * Created on 16.04.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2014/2015 Hannes Pries <http://www.annonyme.de>
  * Permission is hereby granted, free of charge, to any person obtaining a 
  * copy of this software and associated documentation files (the "Software"), 
  * to deal in the Software without restriction, including without limitation 
  * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
  * and/or sell copies of the Software, and to permit persons to whom the 
  * Software is furnished to do so, subject to the following conditions:
  * 
  * The above copyright notice and this permission notice shall be included in 
  * all copies or substantial portions of the Software.
  * 
  * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
  * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
  * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
  * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
  * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
  * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
  * IN THE SOFTWARE.
  */
 
if(!function_exists("boolval")){
	function boolval($value){
		return (bool) $value;
	}
}

use core\utils\dates\XWCalendar;
use PDBC\PDBCSQLStatement;

 /**
  * INSERT INTO DUMMY(ID,NAME) VALUES (#{id},#{name})
  */
class XWSQLStatement extends PDBCSQLStatement{

	public function setDateFromTimeStamp($name,$value){
		$cal = new XWCalendar();
	    $cal->setTime($value);
		$this->setWorkingCopy(
		    preg_replace("/#\{".$name."\}/i","'".$cal->getMySQLDateString()."'",$this->getWorkingCopy())
        );
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param XWCalendar $value
	 */
	public function setDateFromCalendar($name,$value){
        $cal = new XWCalendar();
	    $cal->setTime($value->getTime());
		$this->setWorkingCopy(
		    preg_replace("/#\{".$name."\}/i","'".$cal->getMySQLDateString()."'",$this->getWorkingCopy())
        );
		return $this;
	}
	
	public function setDateWithoutTimeFromCalendar($name,$value){
        $cal = new XWCalendar();
	    $cal->setTime($value->getTime());
		$this->setWorkingCopy(
		    preg_replace("/#\{".$name."\}/i","'".$cal->format("Y-m-d")."'",$this->getWorkingCopy())
        );
		return $this;
	}
} 
