<?php
/*
 * Created on 09.06.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2009/2016 Hannes Pries <http://www.annonyme.de>
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

namespace core\utils\filesystem;
 
class XWFolder{
	private $name="";
	private $path="";
	
	public function __construct(){
		
	}
	
	public function open($path){
		$this->load($path);
	}
	
	public function load($path){
		if(is_dir($path)){
			$parts=preg_split("/\//i",$path);
			$this->name=$parts[count($parts)-1];
			$this->path=$path;
		}		
	}
	
	public function mk(){
		return $this->create();
	}
	
	public function create(){
		if(!is_dir($this->path)){
			$success=mkdir($this->path);
			$this->load($this->path);
			return $success && $this->name!="";
		}
		else{
			return false;
		}
	}
	
	public function delete(){
		if(is_dir($this->path)){
			$success=rmdir($this->path);
			$this->load($this->path);
			return $success && $this->name=="";
		}
		else{
			return false;
		}
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getPath(){
		return $this->path;
	}
	
	public function setName($name){
		$parts=preg_split("/\//i",$name);
		$this->name=$parts[count($parts)-1];
	}
	
	public function setPath($path){
		$this->path=$path;
	}
} 
