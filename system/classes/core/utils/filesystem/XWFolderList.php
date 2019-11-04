<?php
/*
 * Created on 12.06.2009
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

use core\utils\XWArrayList;
use DirectoryIterator;
 
class XWFolderList{
	
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function load($rootPath,$recursive=false){
		if(is_dir($rootPath)){
			$this->list=new XWArrayList();			
			$di=new DirectoryIterator($rootPath);
			foreach($di as $dir){
				if($dir->isDir() && !$dir->isDot()){
					$folder=new XWFolder();
					$folder->load($dir->getPath());
					$this->addFolder($folder);
					if($recursive && !$dir->isDot()){
						$this->load($dir->getPathname(),$recursive);
					}
				}
			}
		}
	}
	
	public function loadByFolder($rootFolder,$recursive=false){
		$this->load($rootFolder->getPath(),$recursive);
	}
	
	/**
	 * @param XWFolder $folder
	 */
	public function addFolder($folder){
		$this->list->add($folder);
	}
	
	/**
	 * @return number
	 */
	public function getSize(){
		return $this->list->size();
	}
	
	/**
	 * @param number $index
	 * @return XWFolder
	 */
	public function getFolder($index){
		return $this->list->get($index);
	}
} 
