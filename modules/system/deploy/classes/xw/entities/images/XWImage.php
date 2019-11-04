<?php
/*
 * Created on 11.07.2007
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

namespace xw\entities\images;

class XWImage{

    private $id=0;
    private $path="";
    private $userId=0;
    private $friends=false;
    private $draft=false;
    private $date="";
    private $title=""; //aoop 0.3.5.5

    public function __construct(){

    }

    public function save(){
        $dao=XWImageManagmentDAO::instance();
        $img=$dao->saveImage($this);
        $this->id=$img->getId();
    }

    public function load($id){
        $dao=XWImageManagmentDAO::instance();
        $img=$dao->loadImage($id);
        $this->id=$img->getId();
        $this->path=$img->getPath();
        $this->userId=$img->getUserId();
        $this->friends=$img->isFriends();
        $this->date=$img->getDate();
        $this->draft=$img->isDraft();  
        $this->title=$img->getTitle();              
    }

    public function delete(){
        $dao=XWImageManagmentDAO::instance();
        $dao->deleteImage($this);
    }

    //------------

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id=$id;
    }

    public function getPath(){
        return $this->path;
    }

    public function setPath($path){
        $this->path=$path;
    }

    public function getUserId(){
        return $this->userId;
    }

    public function setUserId($userId){
        $this->userId=$userId;
    }

    public function setFriends($friends){
        $this->friends=$friends;
    }

    public function isFriends(){
        return $this->friends;
    }
    
    public function setDate($date){
    	$this->date=$date;
    }
    
    public function getDate(){
    	return $this->date;
    }
    
    public function isDraft(){
    	return $this->draft;
    }
    
    public function setDraft($draft){
    	$this->draft=$draft;
    }
    
    public function getTitle(){
    	return $this->title;
    }
    
    public function setTitle($title){
    	$this->title=$title;
    }
}
