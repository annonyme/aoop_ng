<?php
/*
 * Created on 19.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2010 Hannes Pries <http://www.annonyme.de>
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
 
namespace xw\entities\messages;

use core\utils\XWArrayList;
 
 class XWMessageList{
 	private $msgList=null;
 	
 	public function __construct(){
 		$this->msgList=new XWArrayList();
 	}
 	
 	public function loadByUser($user){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListByUser($user);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadByUserAndPage($user,$page=0,$count=20){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListByUserAndPage($user,$page,$count);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadByUserAndUnread($user,$readed=false){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListByUserAndUnread($user,$readed);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadByUserAndPattern($user,$pattern=""){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListByUserAndPattern($user,$pattern);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadBySender($user){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListBySender($user);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadBySenderAndPage($user,$page=0,$count=20){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListBySenderAndPage($user,$page,$count);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	public function loadByConversationUsers($me,$theOther){
 		$this->msgList=new XWArrayList();
 		$dao=new XWMessageManagmentDAO();
 		$list=$dao->loadMessageListByConversationUsers($me,$theOther);
 		for($i=0;$i<$list->getSize();$i++){
 			$this->addMessage($list->getMessage($i));
 		}
 	}
 	
 	//---
 	
 	public function addMessage($message){
 		$this->msgList->add($message);
 	}
 	
 	public function getSize(){
 		return $this->msgList->size();
 	}
 	
 	public function getMessage($index){
 		return $this->msgList->get($index);
 	}
 }
