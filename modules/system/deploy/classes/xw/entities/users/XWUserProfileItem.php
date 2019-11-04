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
 
class XWUserProfileItem{
	
	private $id=0;
	private $name="";
	private $value="";
	private $onlyFriends=false;
	private $dataFormat="string";
	
	private $userId=0;
	
	public function __construct(){
		
	}
	
	public function load($id){
		$dao=XWUserDAO::instance();
		$item=$dao->loadUserProfileItem($id);
		$this->setId($item->getId());
		$this->setName($item->getName());
		$this->setValue($item->getValue());
		$this->setUserId($item->getUserId());
		$this->setDataFormat($item->getDataFormat());
	}
	
	public function save(){
		$dao=XWUserDAO::instance();
		$dao->saveUserProfileItem($this);
	}
	
	public function delete(){
		$dao=XWUserDAO::instance();
		$dao->deleteUserProfileItem($this);
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id=$id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function setValue($value){
		$this->value=$value;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function setUserId($userId){
		$this->userId=$userId;
	}
	
	public function isOnlyFriends(){
		return $this->onlyFriends;
	}
	
	public function setOnlyFriends($onlyFriends){
		$this->onlyFriends=$onlyFriends;
	}
	
	public function getDataFormat(){
		return $this->dataFormat;
	}
	
	public function setDataFormat($dataFormat){
		$this->dataFormat=$dataFormat;
	}
} 
?>
