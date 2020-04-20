<?php
/*
 * Created on 16.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2014 Hannes Pries <http://www.annonyme.de>
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

use core\security\XWScramblingToolKit;
use core\user\UserInterface;
use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use core\database\XWSQLStatement;
use core\database\XWSearchStringParser;
 
class XWUserDAO{
	
	private $db=null;
	
	static private $instance=null;
	 
	static public function instance(){
		if(self::$instance==null){
			self::$instance=new XWUserDAO();
		}
		return self::$instance;
	}

    public function __construct(){
        $dbName=XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
        $this->db=PDBCCache::getInstance()->getDB($dbName);
    }
    
    public function loadUserProfileItem($id){
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    		 "       UP.USERPROFILEITEM_NAME, " .
    		 "       UP.USERPROFILEITEM_VALUE, " .
    		 "       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    		 "       UP.USERPROFILEITEM_DATAFORMAT, " .
    		 "       UUP.USER_ID " .
    		 "FROM XW_USERPROFILEITEMS UP," .
    		 "     XW_USERS_USERPROFILEITEMS UUP " .
    		 "WHERE UP.USERPROFILEITEM_ID=".intval($id)." " .
    		 "  AND UUP.USERPROFILEITEM_ID=UP.USERPROFILEITEM_ID";
    	$db=$this->db;
    	$db->executeQuery($sql);
    	$item=new XWUserProfileItem();
    	for($i=0;$i<$db->getCount();$i++){
    		$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    		$item->setName($db->getResult($i,"USERPROFILEITEM_NAME"));
    		$item->setValue($db->getResult($i,"USERPROFILEITEM_VALUE"));
    		$item->setOnlyFriends($db->getResult($i,"USERPROFILEITEM_ONLYFRIENDS")==1);
    		$item->setDataFormat($db->getResult($i,"USERPROFILEITEM_DATAFORMAT"));
    		$item->setUserId($db->getResult($i,"USER_ID"));
    	}	
    	return $item; 
    }

    /**
     * @param XWUserProfileItem $item
     */
    public function saveUserProfileItem($item){
    	$onlyFriends=0;
    	if($item->isOnlyFriends()){
    		$onlyFriends=1;
    	}
    	
    	if($item->getId()==0){
    		$sql="INSERT INTO XW_USERPROFILEITEMS(" .
    			 " USERPROFILEITEM_NAME, " .
    			 " USERPROFILEITEM_VALUE, " .
    			 " USERPROFILEITEM_CHANGEDATE, " .
    			 " USERPROFILEITEM_ONLYFRIENDS, " .
    			 " USERPROFILEITEM_DATAFORMAT " .
    			 ")" .
    			 "VALUES(" .
    			 " #{name}, " .
    			 " #{value}, " .
    			 " CURRENT_TIMESTAMP," .
    			 " ".$onlyFriends.", " .
    			 " #{dataformat} " .
    			 ")";
    			 
    		$stmt=new XWSQLStatement($sql);
            $stmt->setString("name",$item->getName());	 
            $stmt->setString("value",$item->getValue());	
            $stmt->setString("dataformat",$item->getDataFormat());
    			 
    		$db=$this->db;
    	    $db->execute($stmt->getSQL());
    	    
    	    $sql="SELECT USERPROFILEITEM_ID " .
    	    	 "FROM XW_USERPROFILEITEMS " .
    	    	 "WHERE USERPROFILEITEM_NAME=#{name} " .
    	    	 "  AND USERPROFILEITEM_VALUE=#{value} " .
    	    	 "ORDER BY USERPROFILEITEM_CHANGEDATE";
    	    	 
    	    $stmt=new XWSQLStatement($sql);
            $stmt->setString("name",$item->getName());	 
            $stmt->setString("value",$item->getValue());	 
    	    	 
    	    $db->executeQuery($stmt->getSQL());
    	    for($i=0;$i<$db->getCount();$i++){
    	    	$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    	    }
    	    
    	    $sql="INSERT INTO XW_USERS_USERPROFILEITEMS(USER_ID,USERPROFILEITEM_ID) " .
    	    	 "VALUES (".intval($item->getUserId()).",".intval($item->getId()).")";	
    	    $db->execute($sql); 
    	}
    	else{
    		$sql="UPDATE XW_USERPROFILEITEMS SET " .
    			 "  USERPROFILEITEM_NAME=#{name}, " .
    			 "  USERPROFILEITEM_VALUE=#{value}, " .
    			 "  USERPROFILEITEM_CHANGEDATE=CURRENT_TIMESTAMP, " .
    			 "  USERPROFILEITEM_ONLYFRIENDS=".$onlyFriends.", " .
    			 "  USERPROFILEITEM_DATAFORMAT=#{dataformat} " .
    			 "WHERE USERPROFILEITEM_ID=#{id}";
    			 
    		$stmt=new XWSQLStatement($sql);
            $stmt->setString("name",$item->getName());	 
            $stmt->setString("value",$item->getValue());	
            $stmt->setString("dataformat",$item->getDataFormat());
            $stmt->setInt("id",$item->getId());	 
    			 
    		$db=$this->db;
    	    $db->execute($stmt->getSQL());
    	}
    }

    /**
     * @param XWUserProfileItem $item
     */
    public function deleteUserProfileItem($item){
    	$sql="DELETE FROM XW_USERPROFILEITEMS WHERE USERPROFILEITEM_ID=".intval($item->getId());
    	$db=$this->db;
    	$db->execute($sql);
    	$sql="DELETE FROM XW_USERS_USERPROFILEITEMS WHERE USERPROFILEITEM_ID=".intval($item->getId());
    	$db->execute($sql);
    }

    /**
     * @return XWUserProfileItemList
     */
    public function loadUserProfileItemList(){
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    		 "       UP.USERPROFILEITEM_NAME, " .
    		 "       UP.USERPROFILEITEM_VALUE, " .
    		 "       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    		 "       UP.USERPROFILEITEM_DATAFORMAT, " .
    		 "       UUP.USER_ID " .
    		 "FROM XW_USERPROFILEITEMS UP," .
    		 "     XW_USERS_USERPROFILEITEMS UUP " .
    		 "WHERE UUP.USERPROFILEITEM_ID=UP.USERPROFILEITEM_ID " .
    		 "ORDER BY UP.USERPROFILEITEM_NAME";
    	$db=$this->db;
    	$db->executeQuery($sql);
    	$item=null;
    	$list=new XWUserProfileItemList();
    	for($i=0;$i<$db->getCount();$i++){
    		$item=new XWUserProfileItem();
    		$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    		$item->setName($db->getResult($i,"USERPROFILEITEM_NAME"));
    		$item->setValue($db->getResult($i,"USERPROFILEITEM_VALUE"));
    		$item->setUserId($db->getResult($i,"USER_ID"));
    		$item->setOnlyFriends($db->getResult($i,"USERPROFILEITEM_ONLYFRIENDS")==1);
    		$item->setDataFormat($db->getResult($i,"USERPROFILEITEM_DATAFORMAT"));
    		
    		$list->addUserProfileItem($item);
    	}	
    	return $list; 
    }

    /**
     * @param UserInterface $user
     *
     * @return XWUserProfileItemList
     */
    public function loadUserProfileItemListByUser($user){
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    		 "       UP.USERPROFILEITEM_NAME, " .
    		 "       UP.USERPROFILEITEM_VALUE, " .
    		 "       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    		 "       UP.USERPROFILEITEM_DATAFORMAT, " .
    		 "       UUP.USER_ID " .
    		 "FROM XW_USERPROFILEITEMS UP," .
    		 "     XW_USERS_USERPROFILEITEMS UUP " .
    		 "WHERE UUP.USER_ID=".intval($user->getId())." " .
    		 "  AND UP.USERPROFILEITEM_ID=UUP.USERPROFILEITEM_ID " .
    		 "ORDER BY UP.USERPROFILEITEM_NAME";
	 
    	$db=$this->db;
    	$db->executeQuery($sql);
    	$item=null;
    	$list=new XWUserProfileItemList();
    	for($i=0;$i<$db->getCount();$i++){
    		$item=new XWUserProfileItem();
    		$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    		$item->setName($db->getResult($i,"USERPROFILEITEM_NAME"));
    		$item->setValue($db->getResult($i,"USERPROFILEITEM_VALUE"));
    		$item->setUserId($db->getResult($i,"USER_ID"));
    		$item->setOnlyFriends($db->getResult($i,"USERPROFILEITEM_ONLYFRIENDS")==1);
    		$item->setDataFormat($db->getResult($i,"USERPROFILEITEM_DATAFORMAT"));
    		
    		$list->addUserProfileItem($item);
    	}	
    	return $list;
    }

    /**
     * @param UserInterface $user
     * @param $name
     *
     * @return XWUserProfileItemList
     */
    public function loadUserProfileItemListByUserAndName($user,$name){
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    			"       UP.USERPROFILEITEM_NAME, " .
    			"       UP.USERPROFILEITEM_VALUE, " .
    			"       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    			"       UP.USERPROFILEITEM_DATAFORMAT, " .
    			"       UUP.USER_ID " .
    			"FROM XW_USERPROFILEITEMS UP," .
    			"     XW_USERS_USERPROFILEITEMS UUP " .
    			"WHERE UUP.USER_ID=".intval($user->getId())." " .
    			"  AND UP.USERPROFILEITEM_NAME=#{name} ".
    			"  AND UP.USERPROFILEITEM_ID=UUP.USERPROFILEITEM_ID " .
    			"ORDER BY UP.USERPROFILEITEM_NAME";
    
    	$db=$this->db;
    	$stmt=new XWSQLStatement($sql);
    	$stmt->setString("name", $name);
    	
    	$db->executeQuery($stmt->getSQL());
    	$item=null;
    	$list=new XWUserProfileItemList();
    	for($i=0;$i<$db->getCount();$i++){
    		$item=new XWUserProfileItem();
    		$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    		$item->setName($db->getResult($i,"USERPROFILEITEM_NAME"));
    		$item->setValue($db->getResult($i,"USERPROFILEITEM_VALUE"));
    		$item->setUserId($db->getResult($i,"USER_ID"));
    		$item->setOnlyFriends($db->getResult($i,"USERPROFILEITEM_ONLYFRIENDS")==1);
    		$item->setDataFormat($db->getResult($i,"USERPROFILEITEM_DATAFORMAT"));
    
    		$list->addUserProfileItem($item);
    	}
    	return $list;
    }
    
    public function loadUserProfileItemListByNameAndPattern($name,$pattern){
    	$parser=new XWSearchStringParser();
		$pattern=$parser->simpleStringCleaning($pattern);
		
		$name=$parser->simpleStringCleaning($name);
    	
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    		 "       UP.USERPROFILEITEM_NAME, " .
    		 "       UP.USERPROFILEITEM_VALUE, " .
    		 "       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    		 "       UP.USERPROFILEITEM_DATAFORMAT, " .
    		 "       UUP.USER_ID " .
    		 "FROM XW_USERPROFILEITEMS UP," .
    		 "     XW_USERS_USERPROFILEITEMS UUP " .
    		 "WHERE UP.USERPROFILEITEM_NAME=#{name} " .
    		 "  AND UP.USERPROFILEITEM_VALUE LIKE #{pattern} " .
    		 "  AND UUP.USERPROFILEITEM_ID=UP.USERPROFILEITEM_ID " .
    		 "ORDER BY UP.USERPROFILEITEM_NAME";
    		
    	$stmt=new XWSQLStatement($sql);
        $stmt->setString("name",$name);	
        $stmt->setStringWithWildcards("pattern",$pattern,true,true);	 
    		 
    	$db=$this->db;
    	$db->executeQuery($stmt->getSQL());
    	$item=null;
    	$list=new XWUserProfileItemList();
    	for($i=0;$i<$db->getCount();$i++){
    		$item=new XWUserProfileItem();
    		$item->setId($db->getResult($i,"USERPROFILEITEM_ID"));
    		$item->setName($db->getResult($i,"USERPROFILEITEM_NAME"));
    		$item->setValue($db->getResult($i,"USERPROFILEITEM_VALUE"));
    		$item->setUserId($db->getResult($i,"USER_ID"));
    		$item->setOnlyFriends($db->getResult($i,"USERPROFILEITEM_ONLYFRIENDS")==1);
    		$item->setDataFormat($db->getResult($i,"USERPROFILEITEM_DATAFORMAT"));
    		
    		$list->addUserProfileItem($item);
    	}	
    	return $list;
    }

    /**
     * @param UserInterface $user
     *
     * @return XWUserProfileItemNameList
     */
    public function loadUserProfileItemNameListByUser($user){
    	$sql="SELECT UP.USERPROFILEITEM_ID, " .
    		 "       UP.USERPROFILEITEM_NAME, " .
    		 "       UP.USERPROFILEITEM_VALUE, " .
    		 "       UP.USERPROFILEITEM_ONLYFRIENDS, " .
    		 "       UP.USERPROFILEITEM_DATAFORMAT, " .
    		 "       UUP.USER_ID " .
    		 "FROM XW_USERPROFILEITEMS UP," .
    		 "     XW_USERS_USERPROFILEITEMS UUP " .
    		 "WHERE UUP.USER_ID=".intval($user->getId())." " .
    		 "  AND UP.USERPROFILEITEM_ID=UUP.USERPROFILEITEM_ID " .
    		 "ORDER BY UP.USERPROFILEITEM_NAME";
    	$db=$this->db;
    	$db->executeQuery($sql);

    	$list=new XWUserProfileItemNameList();
    	for($i=0;$i<$db->getCount();$i++){
            $list->addUserProfileItemName($db->getResult($i,"USERPROFILEITEM_NAME"));
    	}	
    	return $list;
    }
    
    public function loadUserDefinedGroup($id){
    	$sql="SELECT UDG.USERDEFINEDGROUP_ID, " .
    		 "       UDG.USERDEFINEDGROUP_NAME, " .
    		 "       UDGC.USER_ID " .
    		 "FROM XW_USERDEFINEDGROUPS UDG, " .
    		 "     XW_CREATORS_USERDEFINEDGROUPS UDGC " .
    		 "WHERE UDG.USERDEFINEDGROUP_ID=".intval($id)."  " .
    		 "  AND UDGC.USERDEFINEDGROUP_ID=UDG.USERDEFINEDGROUP_ID";
    		 
    	$db=$this->db;
    	$db->executeQuery($sql);
    	$group=new XWUserDefinedGroup();
    	for($i=0;$i<$db->getCount();$i++){
    		$group->setId($db->getResult($i,"USERDEFINEDGROUP_ID"));
    		$group->setName($db->getResult($i,"USERDEFINEDGROUP_NAME"));
    		$group->setUserId($db->getResult($i,"USER_ID"));
    	}
    	
    	$list=$this->loadUserListByUserDefinedGroup($group);
    	for($i=0;$i<$list->getSize();$i++){
    		$group->addUser($list->getUser($i));
    	}
    	
    	return $group;
    }

    /**
     * @param XWUserDefinedGroup $group
     *
     * @return XWUserList
     */
    private function loadUserListByUserDefinedGroup($group){
    	$sql="SELECT U.USER_ID, " .
    		 "       U.USER_NAME, " .
    		 "       U.USER_EMAIL, ".
             "       U.USER_ACTIVE, " .
             "       U.USER_PMPOPUP ".
             "FROM XW_USERS U, " .
             "     XW_USERS_USERDEFINEDGROUPS UG ".
             "WHERE UG.USERDEFINEDGROUP_ID=".intval($group->getId())." ".
             "  AND U.USER_ID=UG.USER_ID " .
             "  AND U.USER_ACTIVE=1 ";
        $db=$this->db; 
        $db->executeQuery($sql);
        $userList=new XWUserList();
        $tk=new XWScramblingToolKit();
        for($i=0;$i<$db->getCount();$i++){
            $user=new XWUser();
            $user->setId($db->getResult($i,"USER_ID"));
            $user->setName($db->getResult($i,"USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i,"USER_EMAIL")));
            $user->setActive($db->getResult($i,"USER_ACTIVE")==1);
            $userList->addUser($user);
        }
        return $userList;
    }

    /**
     * @param $name
     * @param UserInterface $user
     *
     * @return XWUserDefinedGroup
     */
    public function loadUserDefinedGroupByNameAndUser($name,$user){
    	$sql="SELECT UDG.USERDEFINEDGROUP_ID, " .
    		 "       UDG.USERDEFINEDGROUP_NAME, " .
    		 "       UDGC.USER_ID " .
    		 "FROM XW_USERDEFINEDGROUPS UDG, " .
    		 "     XW_CREATORS_USERDEFINEDGROUPS UDGC " .
    		 "WHERE UDG.USERDEFINEDGROUP_NAME=#{name}  " .
    		 "  AND UDGC.USERDEFINEDGROUP_ID=UDG.USERDEFINEDGROUP_ID " .
    		 "  AND UDGC.USER_ID=#{id} ";
    		 
    	$stmt=new XWSQLStatement($sql);
        $stmt->setString("name",$name);	 
        $stmt->setString("id",$user->getId());
    		 
    	$db=$this->db;
    	$db->executeQuery($stmt->getSQL());
    	$group=new XWUserDefinedGroup();
    	for($i=0;$i<$db->getCount();$i++){
    		$group->setId($db->getResult($i,"USERDEFINEDGROUP_ID"));
    		$group->setName($db->getResult($i,"USERDEFINEDGROUP_NAME"));
    		$group->setUserId($db->getResult($i,"USER_ID"));
    	}
    	
    	$list=$this->loadUserListByUserDefinedGroup($group);
    	for($i=0;$i<$list->getSize();$i++){
    		$group->addUser($list->getUser($i));
    	}
    	
    	return $group;
    }

    /**
     * @param XWUserDefinedGroup $group
     */
    public function saveUserDefinedGroup($group){
    	if($group->getId()==0){
    		$sql="INSERT INTO XW_USERDEFINEDGROUPS(USERDEFINEDGROUP_NAME) " .
    			 "VALUES(#{name})";
    		
    		$stmt=new XWSQLStatement($sql);
        	$stmt->setString("name",$group->getName());	 
    			 
    		$db=$this->db;
    		$db->execute($stmt->getSQL());
    		$sql="SELECT USERDEFINEDGROUP_ID " .
    			 "FROM XW_USERDEFINEDGROUPS " .
    			 "WHERE USERDEFINEDGROUP_NAME=#{name} " .
    			 "ORDER BY USERDEFINEDGROUP_ID ASC";
    			 
    		$stmt=new XWSQLStatement($sql);
        	$stmt->setString("name",$group->getName());		 
    			 	
    		$db->executeQuery($stmt->getSQL());	  
    		for($i=0;$i<$db->getCount();$i++){
    			$group->setId($db->getResult($i,"USERDEFINEDGROUP_ID"));
    		}	 
    		$sql="INSERT INTO XW_CREATORS_USERDEFINEDGROUPS(USER_ID,USERDEFINEDGROUP_ID) " .
    			 "VALUES(".intval($group->getUserId()).",".intval($group->getId()).")";
    		$db->execute($sql);	 
    	}
    	else{
    		$sql="UPDATE XW_USERDEFINEDGROUPS SET " .
    			 "  USERDEFINEDGROUP_NAME=#{name} " .
    			 "WHERE USERDEFINEDGROUP_ID=#{id}";
    			 
    		$stmt=new XWSQLStatement($sql);
        	$stmt->setString("name",$group->getName());
        	$stmt->setInt("id",$group->getId());
        		 
    		$db=$this->db;
    		$db->execute($stmt->getSQL());
    	}
    }

    /**
     * @param XWUserDefinedGroup $group
     */
    public function deleteUserDefinedGroup($group){
    	$sql="DELETE FROM XW_USERS_USERDEFINEDGROUPS WHERE USERDEFINEDGROUP_ID=".$group->getId();
    	$db=$this->db;
    	$db->execute($sql);
    	$sql="DELETE FROM XW_CREATORS_USERDEFINEDGROUPS WHERE USERDEFINEDGROUP_ID=".$group->getId();
    	$db->execute($sql);
    	$sql="DELETE FROM XW_USERDEFINEDGROUPS WHERE USERDEFINEDGROUP_ID=".$group->getId();
    	$db->execute($sql);
    }

    /**
     * @param UserInterface $user
     * @param XWUserDefinedGroup $group
     */
    public function saveUserToUserDefinedGroup($user,$group){
    	$sql="INSERT INTO XW_USERS_USERDEFINEDGROUPS(USER_ID,USERDEFINEDGROUP_ID) " .
    		 "VALUES (".intval($user->getId()).",".intval($group->getId()).")";
    	$db=$this->db;
    	$db->execute($sql);	 
    }

    /**
     * @param UserInterface$user
     * @param XWUserDefinedGroup $group
     */
    public function removeUserFromUserDefinedGroup($user,$group){
    	$sql="DELETE FROM XW_USERS_USERDEFINEDGROUPS WHERE USER_ID=".$user->getId()."  " .
    		 "   AND USERDEFINEDGROUP_ID=".intval($group->getId());	 
    	$db=$this->db;
    	$db->execute($sql);		 
    }

    /**
     * @param UserInterface $user
     *
     * @return XWUserDefinedGroupList
     */
    public function loadUserDefinedGroupListByUser($user){
    	$sql="SELECT UDG.USERDEFINEDGROUP_ID, " .
    		 "       UDG.USERDEFINEDGROUP_NAME, " .
    		 "       UDGC.USER_ID " .
    		 "FROM XW_USERDEFINEDGROUPS UDG, " .
    		 "     XW_CREATORS_USERDEFINEDGROUPS UDGC " .
    		 "WHERE UDGC.USER_ID=".intval($user->getId())."  " .
    		 "  AND UDG.USERDEFINEDGROUP_ID=UDGC.USERDEFINEDGROUP_ID";
    		 
    	$db=$this->db;
    	$db->executeQuery($sql);
    	$list=new XWUserDefinedGroupList();
    	for($i=0;$i<$db->getCount();$i++){
    		$group=new XWUserDefinedGroup();
    		$group->setId($db->getResult($i,"USERDEFINEDGROUP_ID"));
    		$group->setName($db->getResult($i,"USERDEFINEDGROUP_NAME"));
    		$group->setUserId($db->getResult($i,"USER_ID"));
    		$list->addUserDefinedGroup($group);
    	}
    	
    	return $list;
    }
    
    public function loadLoginLog($id){
    	$sql="SELECT LL.LOGINLOG_ID, " .
    		 "       LL.LOGINLOG_IP, " .
    		 "       LL.LOGINLOG.DATE, " .
    		 "       ULL.USER_ID " .
    		 "FROM XW_LOGINLOGS LL, " .
    		 "     XW_USERS_LOGINLOGS ULL " .
    		 "WHERE LL.LOGINLOG_ID=".intval($id)." " .
    		 "  AND ULL.LOGINLOG_ID=LL.LOGINLOG_ID " .
    		 "ORDER BY LL.LOGINLOG_DATE ASC";
    		 
    	$ll=new XWLoginLog();
    	$db=$this->db;
    	$db->executeQuery($sql);
    	for($i=0;$i<$db->getCount();$i++){
    		$ll->setId($db->getResult($i,"LOGINLOG_ID"));
    		$ll->setIp($db->getResult($i,"LOGINLOG_IP"));
    		$ll->setDate($db->getResult($i,"LOGINLOG_DATE"));
    		$ll->setUserId($db->getResult($i,"USER_ID"));
    	}
    	return $ll;	 
    }

    /**
     * @param XWLoginLog $ll
     */
    public function saveLoginLog($ll){
    	if($ll->getId()==0){
    		$sql="INSERT INTO XW_LOGINLOGS(" .
    			 "  LOGINLOG_IP, " .
    			 "  LOGINLOG_DATE " .
    			 ")" .
    			 "VALUES(" .
    			 "  '".$ll->getIp()."', " .
    			 "  CURRENT_TIMESTAMP " .
    			 ")";
    			 
    		$db=$this->db;
    		$db->execute($sql);
    		
    		$sql="SELECT LOGINLOG_ID " .
    			 "FROM XW_LOGINLOGS " .
    			 "WHERE LOGINLOG_IP='".$ll->getIp()."' " .
    			 "ORDER BY LOGINLOG_DATE ASC";
    			 
    		$db->executeQuery($sql);
    		for($i=0;$i<$db->getCount();$i++){
    			$ll->setId($db->getResult($i,"LOGINLOG_ID"));
    		}	 	 
    		
    		if($ll->getId()>0){
    			$sql="INSERT INTO XW_USERS_LOGINLOGS(USER_ID,LOGINLOG_ID) " .
    				"  VALUES(".intval($ll->getUserId()).",".intval($ll->getId()).")";
    			$db->execute($sql);	
    		}
    	}
    }

    /**
     * @param UserInterface $user
     *
     * @return XWLoginLogList
     */
    public function loadLoginLogListByUser($user){
    	$sql="SELECT LL.LOGINLOG_ID, " .
    		 "       LL.LOGINLOG_IP, " .
    		 "       LL.LOGINLOG.DATE, " .
    		 "       ULL.USER_ID " .
    		 "FROM XW_LOGINLOGS LL, " .
    		 "     XW_USERS_LOGINLOGS ULL " .
    		 "WHERE ULL.USER_ID=".intval($user->getId())." " .
    		 "  AND LL.LOGINLOG_ID=ULL.LOGINLOG_ID " .
    		 "ORDER BY LL.LOGINLOG_DATE ASC";
    		 
    	$llList=new XWLoginLogList();
    	$db=$this->db;
    	$db->executeQuery($sql);
    	for($i=0;$i<$db->getCount();$i++){
    		$ll=new XWLoginLog();
    		$ll->setId($db->getResult($i,"LOGINLOG_ID"));
    		$ll->setIp($db->getResult($i,"LOGINLOG_IP"));
    		$ll->setDate($db->getResult($i,"LOGINLOG_DATE"));
    		$ll->setUserId($db->getResult($i,"USER_ID"));
    		$llList->addLoginLog($ll);
    	}
    	return $llList;	
    } 
    
    public function loadLoginLogListByIp($ip){
    	$sql="SELECT LL.LOGINLOG_ID, " .
    		 "       LL.LOGINLOG_IP, " .
    		 "       LL.LOGINLOG.DATE, " .
    		 "       ULL.USER_ID " .
    		 "FROM XW_LOGINLOGS LL, " .
    		 "     XW_USERS_LOGINLOGS ULL " .
    		 "WHERE LL.LOGINLOG_IP='".$ip."' " .
    		 "  AND ULL.LOGINLOG_ID=LL.LOGINLOG_ID " .
    		 "ORDER BY LL.LOGINLOG_DATE ASC";
    		 
    	$llList=new XWLoginLogList();
    	$db=$this->db;
    	$db->executeQuery($sql);
    	for($i=0;$i<$db->getCount();$i++){
    		$ll=new XWLoginLog();
    		$ll->setId($db->getResult($i,"LOGINLOG_ID"));
    		$ll->setIp($db->getResult($i,"LOGINLOG_IP"));
    		$ll->setDate($db->getResult($i,"LOGINLOG_DATE"));
    		$ll->setUserId($db->getResult($i,"USER_ID"));
    		$llList->addLoginLog($ll);
    	}
    	return $llList;	
    }
    
    /**
     * @return XWUser
     */
    public function getCurrentUser(){
    	$user = new XWUser();
    	if(isset($_SESSION["XWUSER"])){
    		$user = $_SESSION["XWUSER"];
    	}
    	return $user;
    }
    
    public function isCurrentUserValid(){
    	return isset($_SESSION["XWUSER"]) && is_object($_SESSION["XWUSER"]) && $_SESSION["XWUSER"]->getId() > 0;
    }
} 

