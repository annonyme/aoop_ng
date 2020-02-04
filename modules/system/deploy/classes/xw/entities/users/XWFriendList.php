<?php
/*
 * Created on 21.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007 Hannes Pries <http://www.annonyme.de>
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

use core\user\UserInterface;
use core\utils\XWArrayList;
 
 class XWFriendList{
 	
 	private $list=null;
 	
 	public function __construct(){
 		$this->list=new XWArrayList();
 	}
 	
 	public function addUser($user){
 		$this->list->add($user);
 	}
 	
 	/**
 	 * deprecated
 	 */
 	public function addFriend($friend){
 		$this->list->add($friend);
 	}
 	
 	public function getSize(){
 		return $this->list->size();
 	}
 	
 	/**
 	 * deprecated
 	 */
 	public function getFriend($index){
 		return $this->list->get($index);
 	}
 	
 	public function getUser($index){
 		return $this->list->get($index);
 	}

     /**
      * @param UserInterface $user
      *
      * @return bool
      */
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
 	
 	public function loadByUser($user){
 		$this->loadFriendsOfUser($user);
 	}
 	
 	/**
 	 * deprecated
 	 */
 	public function loadFriendsOfUser($user){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$friends=$dao->loadFriendsOfUser($user);
 		for($i=0;$i<$friends->getSize();$i++){
 			$this->addFriend($friends->getUser($i));
 		}
 	}
 	
 	public function loadUsersWhoAddedUserAsFriend($user){
 		$this->list=new XWArrayList();
 		$dao=XWUserManagmentDAO::instance();
 		$friends=$dao->loadUsersWhoAddedUserAsFriend($user);
 		for($i=0;$i<$friends->getSize();$i++){
 			$this->addFriend($friends->getUser($i));
 		}
 	}
 }
