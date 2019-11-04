<?php
/*
 * Created on 16.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2014 Hannes Pries <http://www.annonyme.de>
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

class XWUserProfileItemList{
	
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function addUserProfileItem($item){
		$this->list->add($item);
	}
	
	public function getSize(){
		return $this->list->size();
	}
	
	public function getUserProfileItem($index){
		return $this->list->get($index);
	}
	
	public function getUserProfileItemByName($name){
		$item=null;
		$ritem=new XWUserProfileItem(); //wird zurück gegeben falls noch nicht eines mit dem namen vorhanden ist
		$ritem->setName($name);         //hat also dann id=0 ansonsten das gefunde mit der schon vorhanden id
		for($i=0;$i<$this->getSize();$i++){
			$item=$this->getUserProfileItem($i);
			if($item->getName()==$name){
				$ritem=$item;
			}
		}
		return $ritem;
	}
	
	public function load(){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserProfileItemList();
		$this->list=new XWArrayList();
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserProfileItem($list->getUserProfileItem());
		}
	}
	
	public function loadByUser($user){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserProfileItemListByUser($user);
		$this->list=new XWArrayList();
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserProfileItem($list->getUserProfileItem($i));
		}
	}
	
	public function loadByUserAndName($user,$name){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserProfileItemListByUserAndName($user,$name);
		$this->list=new XWArrayList();
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserProfileItem($list->getUserProfileItem($i));
		}
	}
	
	public function loadByNameAndPattern($name,$pattern){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserProfileItemListByUser($name,$pattern);
		$this->list=new XWArrayList();
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserProfileItem($list->getUserProfileItem());
		}
	}
} 
