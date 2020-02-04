<?php
namespace xw\entities\users;
use core\user\UserInterface;

/*
 * Created on 27.05.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2012/2014 Hannes Pries <http://www.annonyme.de>
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

use core\utils\XWArrayList;

/**
 * @dbtable=XW_USERS
 * @author annonyme
 *
 */
 class XWUser implements UserInterface{
     /**
      * @dbprimary
      * @dbcolumn=USER_ID
      * @dbtype=int
      * @var integer
      */    
     private $id=0;
     
     /**
      * @dbcolumn=USER_NAME
      * @dbtype=string
      * @var string
      */    
     private $name="";
     private $groups=null;
     
     /**
      * @dbcolumn=USER_EMAIL
      * @dbtype=string
      * @var string
      */
     private $email="";
     private $active=false;
     
     private $registrationDate="";
     
     private $useLoginLog=false;

     public function __construct(){
		$this->groups=new XWArrayList();
     }

     public function save($password=""){
         $dao=XWUserManagmentDAO::instance();
         $dao->saveUser($this,$password);
     }

     public function load($id){
         $dao=XWUserManagmentDAO::instance();
         $user=$dao->loadUser($id);
         $this->id=$user->getId();
         $this->name=$user->getName();
         $this->email=$user->getEmail();
         $this->active=$user->isActive();
         $this->setRegistrationDate($user->getRegistrationDate());
         $this->setUseLoginLog($user->isUseLoginLog());
     }
     
     public function loadByName($name){
     	 $dao=XWUserManagmentDAO::instance();
         $user=$dao->loadUserByName($name);
         $this->id=$user->getId();
         $this->name=$user->getName();
         $this->email=$user->getEmail();
         $this->active=$user->isActive();
         $this->setRegistrationDate($user->getRegistrationDate());
         $this->setUseLoginLog($user->isUseLoginLog());
     }

     public function delete(){
         $dao=XWUserManagmentDAO::instance();
         $dao->deleteUser($this);
     }

     public function changePassword($old,$new){
         $dao=XWUserManagmentDAO::instance();
         return $dao->changePasswordUser($this,$old,$new);
     }

     /**
      * @param $newPassword
      * @param UserInterface $adminUser
      *
      * @return bool
      */
     public function changePasswordByAdmin($newPassword,$adminUser){
     	 $admin=new XWUser();
     	 $admin->load($adminUser->getId()); //to get an role-list direkt by the database
     	 //can't be manipulated..
     	 if($admin->isInGroup("admins")){
     	 	 $dao=XWUserManagmentDAO::instance();
     	 	 return $dao->changeUserPasswordByAdmin($this,$newPassword);
     	 }
     	 else{
     	 	return false;
     	 }
     }

     public function login($name,$password){
         $dao=XWUserManagmentDAO::instance();
         $user=$dao->loginUser($name,$password);
         $this->id=$user->getId();
         $this->name=$user->getName();
         $this->email=$user->getEmail();
         $this->active=$user->isActive();
     }

     public function isInGroup($groupName){
         $found=false;             
         if($this->getSize()==0){
         	$this->groups->clear();
         	$dao=XWUserManagmentDAO::instance();
         	$groups=$dao->loadGroupsOfUser($this);
         	for($i=0;$i<$groups->getSize();$i++){
         		$this->addGroup($groups->getGroup($i));
         	}
         }
         $group=null;
         for($i=0;$i<$this->getSize() && !$found;$i++){
         	$group=$this->getGroup($i);
         	if($group->getName()==$groupName || (is_array($groupName) && in_array($group->getName(), $groupName))){
         		$found=true;
         	}
         }
         
         return $found;
     }
     
     public function printGroupDebug(){
     	for($i=0;$i<$this->getSize();$i++){
         	$group=$this->getGroup($i);
         	echo $group->getName()."<br/>\n";
         }         	
     }

     //---
     
     public function addGroup($group){
     	$this->groups->add($group);
     }
     
     public function getSize(){
     	return $this->groups->size();
     }
     
     public function getGroup($index){
     	return $this->groups->get($index);
     }
     
     //---

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

     public function getEmail(){
         return $this->email;
     }

     public function setEmail($email){
         $this->email=$email;
     }

     public function isActive(){
         return $this->active;
     }

     public function setActive($active){
         $this->active=$active;
     }
     
     public function getRegistrationDate(){
     	return $this->registrationDate;
     }
     
     public function setRegistrationDate($registrationDate){
     	$this->registrationDate=$registrationDate;
     }
     
     public function isUseLoginLog(){
     	return $this->useLoginLog;
     }
     
     public function setUseLoginLog($useLoginLog){
     	$this->useLoginLog=$useLoginLog;
     }
 }
