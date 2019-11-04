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
 
 class XWUserInfo{
 	 
 	 private $id=0;
 	 private $userId=0;
 	 private $info="";
 	 private $icq=""; //or aim ...it's the same
 	 private $msn="";
 	 private $homepage="";
 	 private $sex="";
 	 private $single="";
 	 private $location="";
 	 private $age="";
 	 
 	 public function __construct(){
 	 	
 	 }
 	 
 	 public function loadByUser($user){
 	 	 $dao=XWUserManagmentDAO::instance();
 	 	 $info=$dao->loadUserInfoByUser($user);
 	 	 $this->icq=$info->getIcq();
 	 	 $this->msn=$info->getMsn();
 	 	 $this->homepage=$info->getHomepage();
 	 	 $this->sex=$info->getSex();
 	 	 $this->single=$info->getSingle();
 	 	 $this->location=$info->getLocation();
 	 	 $this->age=$info->getAge();
 	 	 $this->info=$info->getInfo();
 	 	 $this->id=$info->getId();
 	 	 $this->userId=$info->getUserId();
 	 }
 	 
 	 public function save(){
 	 	 $dao=XWUserManagmentDAO::instance();
 	 	 $dao->saveUserInfo($this);
 	 }
 	 
 	 //---------
 	 
 	 public function getId(){
 	 	return $this->id;
 	 }
 	 
 	 public function setId($id){
 	 	$this->id=$id;
 	 }
 	 
 	 public function getUserId(){
 	 	return $this->userId;
 	 }
 	 
 	 public function setUserId($userId){
 	 	$this->userId=$userId;
 	 }
 	 
 	 public function getInfo(){
 	 	return $this->info;
 	 }
 	 
 	 public function setInfo($info){
 	 	$this->info=$info;
 	 }
 	 
 	 public function getIcq(){
 	 	return $this->icq;
 	 }
 	 
 	 public function setIcq($icq){
 	 	$this->icq=$icq;
 	 }
 	 
 	 public function getMsn(){
 	 	return $this->msn;
 	 }
 	 
 	 public function setMsn($msn){
 	 	$this->msn=$msn;
 	 }
 	 
 	 public function getHomepage(){
 	 	return $this->homepage;
 	 }
 	 
 	 public function setHomepage($homepage){
 	 	$this->homepage=$homepage;
 	 }
 	 
 	 public function getSex(){
 	 	return $this->sex;
 	 }
 	 
 	 public function setSex($sex){
 	 	$this->sex=$sex;
 	 }
 	 
 	 public function getSingle(){
 	 	return $this->single;
 	 }
 	 
 	 public function setSingle($single){
 	 	$this->single=$single;
 	 }
 	 
 	 public function getLocation(){
 	 	return $this->location;
 	 }
 	 
 	 public function setLocation($location){
 	 	$this->location=$location;
 	 }
 	 
 	 public function getAge(){
 	 	return $this->age;
 	 }
 	 
 	 public function setAge($age){
 	 	$this->age=$age;
 	 }
 }
