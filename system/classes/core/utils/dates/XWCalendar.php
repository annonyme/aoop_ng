<?php
/*
 * Created on 04.12.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2014/2016 Hannes Pries <http://www.annonyme.de>
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

namespace core\utils\dates;
 
class XWCalendar{
	
	public  $DAY=0;
	public  $MONTH=1;
	public  $YEAR=2;
	public  $HOUR=3;
	public  $MINUTE=4;
	public  $SECOND=5;
	
	public  $DAY_OF_MONTH=6;
	public  $DAY_OF_WEEK=7;
	public  $WEEK_OF_YEAR=8;
	public  $ISO_YEAR=9;
	public  $ISO_WEEK_OF_YEAR=10;
	
	public  $MICROSECONDS=11;
	public  $DAY_NAME=12;
	public  $DAY_OF_YEAR=13;
	public  $MONTH_NAME=14;
	
	
	private $currentDate=0;
	
	
	public function __construct($current=0){
		if($current==0){
			$current=time();
		}
		$this->currentDate=$current;
	}
	
	public function isLeapYear(){
		$year=$this->get($this->YEAR);
		if ($year%400==0 || ($year%4==0 && $year%100!=0)){
           return true;
    	}
    	else{
        	return false;
    	}
	}
	
	public function get($field){
		switch ($field) {
			case $this->DAY:
				return date("d",$this->currentDate);
				break;
				
			case $this->MONTH:
				return date("m",$this->currentDate);
				break;
				
			case $this->YEAR:
				return date("Y",$this->currentDate);
				break;
		
			case $this->HOUR:
				return date("H",$this->currentDate);
				break;
		
			case $this->MINUTE:
				return date("i",$this->currentDate);
				break;
				
			case $this->SECOND:
				return date("s",$this->currentDate);
				break;
		
			case $this->DAY_OF_MONTH:
				return date("d",$this->currentDate);
				break;
		
			case $this->DAY_OF_WEEK:
				return date("N",$this->currentDate);
				break;
		
			case $this->WEEK_OF_YEAR:
				return date("W",$this->currentDate);
				break;
				
			case $this->ISO_YEAR:
				return date("o",$this->currentDate);
				break;
		
			case $this->ISO_WEEK_OF_YEAR:
				return date("W",$this->currentDate);
				break;
		
			case $this->MICROSECONDS:
				return date("u",$this->currentDate);
				break;
		
			case $this->DAY_NAME:
				return date("l",$this->currentDate);
				break;
		
			case $this->DAY_OF_YEAR:
				return date("z",$this->currentDate);
				break;
				
			case $this->MONTH_NAME:
				return date("F",$this->currentDate);
				break;	
		
			default:
				break;
		}
		return null;
	}
	
	public function sub($field,$amount){
		return $this->add($field,$amount*-1);
	}
	
	/**
	 * @param int $field
	 * @param int $amount
	 * @return XWCalendar
	 */
	public function add($field,$amount){
		switch ($field) {
			case $this->DAY:				
				$this->currentDate=$this->currentDate+($amount*1 * 24 * 60 * 60);
				break;
				
			case $this->MONTH:
				$this->currentDate=$this->currentDate+($amount*30 * 24 * 60 * 60);
				break;
				
			case $this->YEAR:
				$this->currentDate+=($amount*365* 24 * 60 * 60);
				break;
		
			case $this->HOUR:
				$this->currentDate+=($amount* 60 * 60);
				break;
		
			case $this->MINUTE:
				$this->currentDate+=($amount* 60);
				break;
				
			case $this->SECOND:
				$this->currentDate+=($amount);
				break;
		
			case $this->DAY_OF_MONTH:
				$this->currentDate+=($amount*1 * 24 * 60 * 60);
				break;
		
			case $this->DAY_OF_WEEK:
				$this->currentDate+=($amount*1 * 24 * 60 * 60);
				break;
		
			case $this->WEEK_OF_YEAR:
				$this->currentDate+=($amount*7 * 24 * 60 * 60);
				break;
				
			case $this->ISO_YEAR:
				$this->currentDate+=($amount*365* 24 * 60 * 60);
				break;
		
			case $this->ISO_WEEK_OF_YEAR:
				$this->currentDate+=($amount*7 * 24 * 60 * 60);
				break;
		
			case $this->MICROSECONDS:
				$this->currentDate+=($amount);
				break;
		
			case $this->DAY_NAME:
				$this->currentDate+=($amount*1 * 24 * 60 * 60);
				break;
		
			case $this->DAY_OF_YEAR:
				$this->currentDate+=($amount*1 * 24 * 60 * 60);
				break;	
		
			default:
				break;
		}
		
		return $this;
	}
	
	/**
	 * @return number
	 */
	public function getTime(){
		return intval($this->currentDate);
	}
	
	//Java Date style (also milliseconds)
	public function setTime($time){
		$this->currentDate=$time;
	}
	
	//Java GregorianCalendar style (2009/02/23 update)
	public function setTimeInMillis($time){
		$this->setTime(intval($time/1000));
	}
	
	public function getTimeInMillis(){
		return $this->getTime() * 1000;
	}
	
	public function getMySQLDateString($pattern="Y-m-d H:i:s"){
		return $this->format($pattern);
	}
	
	public function toString(){
		return $this->getMySQLDateString();
	}
	
	/**
	 * default is Y-m-d H:i:s
	 */
	public function format($pattern="Y-m-d H:i:s"){
		//date_default_timezone_set("UTC"); TODO other way to do it.. or suppress warning
		return date($pattern, (int) $this->currentDate);
	}
	
	public function setMySQLDateString($mysql){
		if(strlen($mysql)<11){
			$mysql=trim($mysql)." 00:00:00";
		}
		
		$datetime=preg_split("/\s/",$mysql);
		$date=preg_split("/-/",$datetime[0]);
		$time=preg_split("/:/",$datetime[1]);
		
		//date_default_timezone_set("UTC");
		$this->currentDate=mktime((int) $time[0],(int) $time[1],(int) $time[2],(int) $date[1],(int) $date[2],(int) $date[0]);
	}
	
	/**
	 * 
	 */
	public function setToMondayByYearAndWeek($year,$week){
		$year=intval($year);
		$week=intval($week);
		if(strlen($week)==1){
			$week="0".$week;
		}
		$this->currentTime=strtotime($year."W".$week."1");
		return $this;
	}
	
	public function getDifferenceAsNewCalendar(XWCalendar $date){
		return new XWCalendar($date->getTime()-$this->currentDate);
	} 
	
	public function roundToDay($plusOneSec=false){
		//TODO later mathematical: floor($time/86400)*86400;
		$day=$this->get($this->DAY);
		$month=$this->get($this->MONTH);
		$year=$this->get($this->YEAR);
		
		$sec=0;
		if($plusOneSec){
			$sec=1;
		}
		
		$this->currentDate=mktime(0,0,$sec,$month,$day,$year);
		
		return $this;
	}
	
	public function roundToMonth($plusOneSec=false){
		$month=$this->get($this->MONTH);
		$year=$this->get($this->YEAR);
		
		$sec=0;
		if($plusOneSec){
			$sec=1;
		}
		
		$this->currentDate=mktime(0,0,$sec,$month,1,$year);
		
		return $this;
	}
	
	public function copyCalendar(){
		$cal=new XWCalendar();
		$cal->setTime($this->currentDate);
		return $cal;
	}
}
