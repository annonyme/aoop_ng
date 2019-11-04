<?php
/*
 * Created on 19.06.2007
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
 
 class XWFriend{
 	private $userId=0;
 	private $friendId=0;
 	
 	public function __construct(){
 		
 	}
 	
 	/**
 	 * is $friend a friend of $user?  ($user,$friend)
 	 */
 	public function isAFriend($user=null,$friend=null){
 		$dao=XWUserManagmentDAO::instance();
 		if($user==null){
 			$user=new XWUser();
 			$user->load($this->userId);
 		}
 		if($friend==null){
 			$friend=new XWUser();
 			$friend->load($this->friendId);
 		}
 		return $dao->isUserFriend($user,$friend);
 	}
 	
 	public function save(){
 		$dao=XWUserManagmentDAO::instance();
 		$you=new XWUser();
 		$other=new XWUser();
 		$you->load($this->userId);
 		$other->load($this->friendId);
 		$dao->addFriend($you,$other);
 	}
 	
 	public function delete(){
 		$dao=XWUserManagmentDAO::instance();
 		$you=new XWUser();
 		$other=new XWUser();
 		$you->load($this->userId);
 		$other->load($this->friendId);
 		$dao->removeFriend($you,$other);
 	}
 	
 	//---
 	
 	public function getUserId(){
 		return $this->userId;
 	}
 	
 	public function setUserId($userId){
 		$this->userId=$userId;
 	}
 	
 	public function getFriendId(){
 		return $this->friendId;
 	}
 	
 	public function setFriendId($friendId){
 		$this->friendId=$friendId;
 	}
 }
?>
