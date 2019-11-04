<?php
/*
 * Created on 17.01.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2012 Hannes Pries <http://www.annonyme.de>
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
 
class XWLoginLog{
	
	private $id=0;
	private $ip="";
	private $date="";
	
	private $userId=0;
	
	public function __construct(){
		
	}
	
	public function load($id){
		$dao=XWUserDAO::instance();
		$log=$dao->loadLoginLog($id);
		
		$this->setId($log->getId());
		$this->setIp($log->getIp());
		$this->setDate($log->getDate());
		$this->setUserId($log->getUserId());
	}
	
	public function save(){
		$dao=XWUserDAO::instance();
		$dao->saveLoginLog($this);
	}
	
	public function delete(){
		
	} 
	
	public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id=$id;
    }
    
    public function getIp(){
    	return $this->ip;
    }
    
    public function setIp($ip){
    	$this->ip=$ip;
    }
    
    public function getDate(){
    	return $this->date;
    }
    
    public function setDate($date){
    	$this->date=$date;
    }
    
    public function getUserId(){
    	return $this->userId;
    }
    
    public function setUserId($userId){
    	$this->userId=$userId;
    }
} 
