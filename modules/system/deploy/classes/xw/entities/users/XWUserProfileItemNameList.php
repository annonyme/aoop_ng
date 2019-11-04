<?php
/*
 * Created on 16.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008 Hannes Pries <http://www.annonyme.de>
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

namespace xw\entities\users;

use core\utils\XWArrayList;
 
class XWUserProfileItemNameList{
	
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function addUserProfileItemName($name){
		$this->list->add($name);
	}
	
	public function getSize(){
		return $this->list->size();
	}
	
	public function getUserProfileItemName($index){
		return $this->list->get($index);
	}
	
	public function load(){
		$this->list=new XWArrayList();
		$list=new XWUserProfileItemList();
		$list->load();
		$item=null;
		for($i=0;$i<$list->getSize();$i++){
			$item=$list->getUserProfileItem($i);
			$this->addUserProfileItemName($item->getName());
		}
	}
	
	public function loadByUser($user){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserProfileItemNameListByUser($user);
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserProfileItemName($list->getUserProfileItemName($i));
		}
	}
}
