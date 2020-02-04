<?php
/*
 * Created on 22.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2008/2010/2012/2014 Hannes Pries <http://www.annonyme.de>
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

use core\events\EventListenerFactory;
use core\user\UserInterface;
use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use core\database\XWSQLStatement; 
use core\database\XWSearchStringParser;

 class XWMessageManagmentDAO{
 	private $db=null;
 	
 	public function __construct(){
 		$dbName=XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
        $this->db=PDBCCache::getInstance()->getDB($dbName);
 	}
 	
 	public function loadMessage($id){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_ID=".intval($id);
 		$db=$this->db; 
        $db->executequery($sql);
        $msg=new XWMessage();
        for($i=0;$i<$db->getCount();$i++){
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
			$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        }
        return $msg;
 	}

	 /**
	  * @param XWMessage $msg
	  */
 	public function saveMessage($msg){
 		//nur insert 		
 		if($msg->getId()==0){
 			$sql="INSERT INTO XW_MSGS(" .
 				 "  MSG_TO," .
 				 "  MSG_FROM," .
 				 "  MSG_HEADER," .
	 			 "  MSG_TITLE," .
	 			 "  MSG_CONTENT," .
 				 "  MSG_DATE," .
 				 "  MSG_VIEWED)" .
 				 "VALUES (" .
 				 "".intval($msg->getTo())."," .
 				 "".intval($msg->getFrom())."," .
 				 "#{header}," .
 				 "#{title}," .
 				 "#{content}," .
 				 "CURRENT_TIMESTAMP," .
 				 "0)";
 				 
 			$stmt=new XWSQLStatement($sql);
            $stmt->setString("title",$msg->getTitle());	
            $stmt->setString("content",$msg->getContent());	 
            $stmt->setString("header",$msg->getHeader());	
 				 	 
 			$db=$this->db;
  	 	    $db->execute($stmt->getSQL());

			EventListenerFactory::getInstance()->fireFilterEvent('System_Messages_Saved', $msg, []);
 		} 		 
 	}

	 /**
	  * @param XWMessage $msg
	  */
 	public function deleteMessage($msg){
 		$sql="UPDATE XW_MSGS SET MSG_DELETED=1 WHERE MSG_ID=".intval($msg->getId());
 		$db=$this->db; 
        $db->execute($sql);
 	}

	 /**
	  * @param XWMessage $msg
	  */
 	function setViewedForMessage($msg){
 		$sql="UPDATE XW_MSGS SET " .
 			 " MSG_VIEWED=1 " .
 			 "WHERE MSG_ID=".intval($msg->getId());
 		$db=$this->db; 
        $db->execute($sql);
 	}
 	
 	//------ lists

	 /**
	  * @param UserInterface $user
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByUser($user){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_TO=".intval($user->getId()). " " .
 			 "  AND MSG_DELETED=0 " .
 			 "ORDER BY MSG_DATE DESC " .
 			 "LIMIT 0,100 ";
 		$db=$this->db; 
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $user
	  * @param int $page
	  * @param int $count
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByUserAndPage($user,$page=0,$count=20){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_TO=".intval($user->getId()). " " .
 			 "  AND MSG_DELETED=0 " .
 			 "ORDER BY MSG_DATE DESC " .
 			 "LIMIT ".(intval($page)*intval($count)).",".((intval($page)+1)*intval($count))." ";
 			  
 		$db=$this->db; 
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $user
	  * @param $pattern
	  * @param int $page
	  * @param int $count
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByUserAndPatternAndPage($user,$pattern,$page=0,$count=20){
 		if(trim($pattern)==""){
 			return $this->loadMessageListByUserAndPage($user,$page,$count);
 		}
 		else{
 			$parser=new XWSearchStringParser();
			$pattern=$parser->simpleStringCleaning($pattern);
	 		
	 		$sql="SELECT MSG_ID, " .
	 			 "       MSG_TO, " .
	 			 "       MSG_FROM, " .
	 			 "       MSG_HEADER, " .
	 			 "       MSG_CONTENT, " .
	 			 "       MSG_DATE, " .
	 			 "       MSG_VIEWED, " .
	 			 "       MSG_DELETED," .
	 			 "       MSG_TITLE " .
	 			 "FROM XW_MSGS " .
	 			 "WHERE MSG_TO=".intval($user->getId()). " " .
	 			 "  AND MSG_DELETED=0 " .
	 			 "  AND (MSG_CONTENT LIKE #{pattern}" .
	 			 "       OR MSG_TITLE LIKE #{pattern})" .
	 			 "ORDER BY MSG_DATE DESC " .
	 			 "LIMIT ".(intval($page)*intval($count)).",".((intval($page)+1)*intval($count))." ";
	 			  
	 		$stmt=new XWSQLStatement($sql);
            $stmt->setStringWithWildcards("pattern",$pattern,true,true);		  
	 			  
	 		$db=$this->db; 
	        $db->executequery($stmt->getSQL());
	        $msgList=new XWMessageList();
	        for($i=0;$i<$db->getCount();$i++){
	        	$msg=new XWMessage();
	        	$msg->setId($db->getResult($i,"MSG_ID"));
	        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
	        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
	        	$msg->setDate($db->getResult($i,"MSG_DATE"));
	        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
				$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
	        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
	        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
	        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
	        	
	        	$msgList->addMessage($msg);
	        }
	        return $msgList;
 		} 		
 	}

	 /**
	  * @param UserInterface $user
	  * @param bool $readed
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByUserAndUnread($user,$readed=false){
 		$readedInt=0;
 		if($readed){
 			$readedInt=1;
 		}
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_TO=".intval($user->getId()). " " .
 			 "  AND MSG_DELETED=0 " .
 			 "  AND MSG_VIEWED=".$readedInt."  " .
 			 "ORDER BY MSG_DATE DESC";
 		$db=$this->db; 
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $user
	  * @param string $pattern
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByUserAndPattern($user,$pattern=""){
 		$parser=new XWSearchStringParser();
		$pattern=$parser->simpleStringCleaning($pattern);
		
 		//$pattern="%".$pattern."%";
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED," .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_TO=".intval($user->getId()). " " .
 			 "  AND MSG_CONTENT LIKE #{pattern} " .
 			 "  AND MSG_DELETED=0 " .
 			 "ORDER BY MSG_DATE DESC";
 			 
 		$stmt=new XWSQLStatement($sql);
        $stmt->setStringWithWildcards("pattern",$pattern,true,true);		 
 			 
 		$db=$this->db;
        $db->executequery($stmt->getSQL());
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $user
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListBySender($user){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_FROM=".intval($user->getId()). " " .
 			 "ORDER BY MSG_DATE DESC " .
 			 "LIMIT 0,100";
 		$db=$this->db;
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $user
	  * @param int $page
	  * @param int $count
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListBySenderAndPage($user,$page=0,$count=20){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE MSG_FROM=".intval($user->getId()). " " .
 			 "ORDER BY MSG_DATE DESC " .
 			 "LIMIT ".(intval($page)*intval($count)).",".((intval($page)+1)*intval($count))." ";
 			  
 		$db=$this->db;
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}

	 /**
	  * @param UserInterface $me
	  * @param UserInterface $theOther
	  *
	  * @return XWMessageList
	  */
 	public function loadMessageListByConversationUsers($me,$theOther){
 		$sql="SELECT MSG_ID, " .
 			 "       MSG_TO, " .
 			 "       MSG_FROM, " .
 			 "       MSG_HEADER, " .
 			 "       MSG_CONTENT, " .
 			 "       MSG_DATE, " .
 			 "       MSG_VIEWED, " .
 			 "       MSG_DELETED," .
 			 "       MSG_TITLE " .
 			 "FROM XW_MSGS " .
 			 "WHERE (MSG_FROM=".intval($me->getId()). " AND MSG_TO=".intval($theOther->getId()).") " .
 			 "   OR (MSG_FROM=".intval($theOther->getId()). " AND MSG_TO=".intval($me->getId()).") " .
 			 "ORDER BY MSG_DATE DESC " .
 			 "LIMIT 0,30"; 
 		$db=$this->db;
        $db->executequery($sql);
        $msgList=new XWMessageList();
        for($i=0;$i<$db->getCount();$i++){
        	$msg=new XWMessage();
        	$msg->setId($db->getResult($i,"MSG_ID"));
        	$msg->setHeader($db->getResult($i,"MSG_HEADER"));
        	$msg->setContent($db->getResult($i,"MSG_CONTENT"));
        	$msg->setDate($db->getResult($i,"MSG_DATE"));
        	$msg->setViewed($db->getResult($i,"MSG_VIEWED")==1);
			$msg->setDeleted($db->getResult($i,"MSG_DELETED")==1);
        	$msg->setTitle($db->getResult($i,"MSG_TITLE"));
        	$msg->setUserId($db->getResult($i,"MSG_FROM"));
        	$msg->setReceiverId($db->getResult($i,"MSG_TO"));
        	
        	$msgList->addMessage($msg);
        }
        return $msgList;
 	}
 }

