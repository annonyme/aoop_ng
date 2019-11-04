<?php

/*
  * Copyright (c) 2007/2009 Hannes Pries <http://www.annonyme.de>
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

use core\utils\XWArrayList;

class XWGroupList{

    private $list=null;
    
    public function __construct(){
		$this->list=new XWArrayList();
    }

    public function addGroup($group){
        $this->list->add($group);
    }

    public function getSize(){
        return $this->list->size();
    }

    /**
     * 
     * @param int $index
     * @return XWGroup
     */
    public function getGroup($index){
        return $this->list->get($index);
    }

    public function load(){
        $this->list=new XWArrayList();
        $dao=XWUserManagmentDAO::instance();
        $groups=$dao->loadGroupList();
        for($i=0;$i<$groups->getSize();$i++){
        	$this->addGroup($groups->getGroup($i));
        } 
    }

    public function loadByUser($user){
        $this->list=new XWArrayList();
        $dao=XWUserManagmentDAO::instance(); 
        $groups=$dao->loadGroupsOfUser($user);
        for($i=0;$i<$groups->getSize();$i++){
        	$this->addGroup($groups->getGroup($i));
        }  
    }
    
    public function existsIn($group){
    	$dummy=null;
        $found=false;
        for($i=0;$i<$this->getSize();$i++){
        	$dummy=$this->getGroup($i);
        	if($dummy->getId()==$group->getId()){
        		$found=true;
        		return $found;
        	}
        }
        return $found;
    }
}
