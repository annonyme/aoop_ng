<?php
/*
 * Created on 07.09.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace xw\entities\users;

use core\utils\XWArrayList;
 
class XWUserDefinedGroup{
	
	private $id=0;
	private $name="";
	private $users=null;
	
	private $userId=0;
	
	public function __construct(){
		$this->users=new XWArrayList();
	}
	
	public function load($id){
		$dao=XWUserDAO::instance();
		$group=$dao->loadUserDefinedGroup($id);
		$this->setId($group->getId());
		$this->setName($group->getName());
		$this->setUserId($group->getUserId());
		
		$this->users=new XWArrayList();
		for($i=0;$i<$group->getSize();$i++){
			$this->addUser($group->getUser($i));
		}
	}
	
	public function loadByNameAndUser($name,$user){
		$dao=XWUserDAO::instance();
		$group=$dao->loadUserDefinedGroupByNameAndUser($name,$user);
		$this->setId($group->getId());
		$this->setName($group->getName());
		$this->setUserId($group->getUserId());
		
		$this->users=new XWArrayList();
		for($i=0;$i<$group->getSize();$i++){
			$this->addUser($group->getUser($i));
		}
	}
	
	public function save(){
		$dao=XWUserDAO::instance();
		$dao->saveUserDefinedGroup($this);
	}
	
	public function delete(){
		$dao=XWUserDAO::instance();
		$dao->deleteUserDefinedGroup($this);
	}
	
	public function saveUserTo($user){
		$dao=XWUserDAO::instance();
		$dao->saveUserToUserDefinedGroup($user,$this);
	}
	
	public function removeUserFrom($user){
		$dao=XWUserDAO::instance();
		$dao->removeUserFromUserDefinedGroup($user,$this);
	}
	
	public function addUser($user){
		$this->users->add($user);
	}
	
	public function getSize(){
		return $this->users->size();
	}
	
	public function getUser($index){
		return $this->users->get($index);
	}
	
	public function existsIn($user){
		$found=false;
		$dummy=null;
		for($i=0;$i<$this->getSize();$i++){
			$dummy=$this->getUser($i);
			if($user->getId()==$dummy->getId()){
				$found=true;
				return $found;
			}
		}
		return $found;
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
    
    public function getUserId(){
		return $this->userId;
	}
	
	public function setUserId($userId){
		$this->userId=$userId;
	}
}
