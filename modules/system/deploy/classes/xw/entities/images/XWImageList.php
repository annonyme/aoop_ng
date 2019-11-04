<?php
/*
 * Created on 11.07.2007
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

namespace xw\entities\images;

use core\utils\XWArrayList;
 
class XWImageList{
    private $list=null;
    
    public function __construct(){
    	$this->list=new XWArrayList();
    }

    public function loadByUser($user){
        $dao=XWImageManagmentDAO::instance();
        $list=$dao->loadListByUser($user);
        $this->list=new XWArrayList();
        for($i=0;$i<$list->getSize();$i++){
        	$this->addImage($list->getImage($i));
        }
    }
    
    public function loadByLatest($limit="10"){
    	$dao=XWImageManagmentDAO::instance();
        $list=$dao->loadListByLatest($limit);
        $this->list=new XWArrayList();
        for($i=0;$i<$list->getSize();$i++){
        	$this->addImage($list->getImage($i));
        }
    }

    //---

    public function addImage($image){
        $this->list->add($image);         
    }

    public function getImage($index){
        return $this->list->get($index);
    }

    public function getSize(){
        return $this->list->size();
    }
} 
