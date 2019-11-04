<?php
/*
 * Created on 23.02.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
  * Copyright (c) 2009/2012 Hannes Pries <http://www.annonyme.de>
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

use core\utils\filesystem\XWFileList;
use core\utils\dates\XWCalendar;
 
class XWTempFolderCleaner{
	private $folder="temp/";
	private $timeOffsetInHours=-5;
	private $stepsPerRun=20;
	
	public function __construct(){
		$this->performCleaning();
	}
	
	public function performCleaning(){		
		if(is_dir($this->folder)){			
			$list=new XWFileList();
			$list->load($this->folder);
			if($list->getSize()>0){
				$cal=new XWCalendar();
				$cal->add($cal->HOUR,$this->timeOffsetInHours);
				
				$start=0;
				if(isset($_SESSION["tempclean_start"]) && $_SESSION["tempclean_start"]<$list->getSize()){
					$start=$_SESSION["tempclean_start"];
					$_SESSION["tempclean_start"]=$_SESSION["tempclean_start"]+$this->stepsPerRun;
				}
				else{
					$_SESSION["tempclean_start"]=$start;
				}
				
				$direction="up";
				if(isset($_SESSION["tempclean_direction"])){
					$direction=$_SESSION["tempclean_direction"];
				}
				else{
					if(mt_rand(0,2)==0){
						$direction="up";
					}
					else{
						$direction="down";
					}
					$_SESSION["tempclean_direction"]=$direction;
				}
				
				if($direction=="up"){
					$size=$list->getSize(); 
					for($i=$start;($i<$size && $i<$start+$this->stepsPerRun);$i++){			
						if(filemtime($this->folder.$list->getFile($i))<$cal->getTime()){
							try{
								unlink($this->folder.$list->getFile($i));
							}
							catch(Exception $e){
							
							}
						}
					}
				}
				else{
					$size=$list->getSize();
					for($i=$size-($start+1);($i>=0 && $i>$size-($start+$this->stepsPerRun));$i--){			
						if(filemtime($this->folder.$list->getFile($i))<$cal->getTime()){
							try{
								unlink($this->folder.$list->getFile($i));
							}
							catch(Exception $e){
							
							}
						}
					}
				}	
			}						
		}
	}
	
	public function getFolder(){
		return $this->folder;
	}
	
	public function setFolder($folder){
		$this->folder=$folder;
	}
	
	public function getTimeOffsetInHours(){
		return $this->timeOffsetInHours;
	}
	
	public function setTimeOffsetInHours($timeOffsetInHours){
		$this->timeOffsetInHours=$timeOffsetInHours;
	}
	
	public function getStepsPerRun(){
		return $this->stepsPerRun;
	}
	
	public function setStepsPerRun($stepsPerRun){
		$this->stepsPerRun=$stepsPerRun;
	}
} 
?>
