<?php
/*
 * Created on 20.02.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2009/2014/2016 Hannes Pries <http://www.annonyme.de>
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

use core\utils\XWArrayList;
use DirectoryIterator;
use Exception;

class XWFileList{
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function addFile($file){
 		$this->list->add($file);
 	}
 	
 	public function getSize(){
 		return $this->list->size();
 	}
 	
 	public function getFile($index){
 		return $this->list->get($index);
 	}
 	
 	public function load($path){
 		$this->list->clear();
 		try{
            $di=new DirectoryIterator($path);
            foreach($di as $file){
            	if($file->isFile()){
            		$this->addFile($file->getFilename());
            	}
            }
 		}
 		catch(Exception $e){
 			
 		}
 	}
}
