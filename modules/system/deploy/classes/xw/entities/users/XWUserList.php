<?php
/*
 * Created on 27.05.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2009/2010/2011/2014 Hannes Pries <http://www.annonyme.de>
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

 class XWUserList{
 	private $list=null;
 	
 	public function __construct(){
 		$this->list=new XWArrayList();
 	}
 	
 	public function addUser($user){
 		$this->list->add($user);
 	}
 	
 	public function getSize(){
 		return $this->list->size();
 	}
 	
 	public function getUser($index): XWUser{
 		return $this->list->get($index);
 	}
 	
 	public function existsInByEmail($email){
 		$found=false;
 		$dummy=null;
 		for($i=0;$i<$this->getSize();$i++){
 			$dummy=$this->getUser($i);
 			if($dummy->getEmail()==$email){
 				$found=true;
 				return $found;
 			}
 		}
 		return $found;
 	}
 	
 	public function existsInByName($name){
 		$found=false;
 		$dummy=null;
 		for($i=0;$i<$this->getSize();$i++){
 			$dummy=$this->getUser($i);
 			if($dummy->getName()==$name){
 				$found=true;
 				return $found;
 			}
 		}
 		return $found;
 	}
 	
 	public function existsIn($user){
 		$found=false;
 		$dummy=null;
 		for($i=0;$i<$this->getSize();$i++){
 			$dummy=$this->getUser($i);
 			if($dummy->getId()==$user->getId()){
 				$found=true;
 				return $found;
 			}
 		}
 		return $found;
 	}
 	
 	//--
 	
 	public function load($active=true){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$users=$dao->loadUserList($active);
 		for($i=0;$i<$users->getSize();$i++){
 			$this->addUser($users->getUser($i));
 		}
 	}
 	
 	public function loadByActive($active=true){
 		$this->load($active);
 	}
 	
 	public function loadByFirstLetter($letter="a",$active=true){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$users=$dao->loadUserListByFirstLetter($letter,$active);
 		for($i=0;$i<$users->getSize();$i++){
 			$this->addUser($users->getUser($i));
 		}
 	}
	
	public function loadByPattern($pattern="a",$active=true){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$users=$dao->loadUserListByPattern($pattern,$active);
 		for($i=0;$i<$users->getSize();$i++){
 			$this->addUser($users->getUser($i));
 		}
 	}
 	
 	public function loadByPageAndCount($page=0,$count=20,$active=true){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$users=$dao->loadUserListByPageAndCount($page,$count,$active);
 		for($i=0;$i<$users->getSize();$i++){
 			$this->addUser($users->getUser($i));
 		}
 	}
 }
