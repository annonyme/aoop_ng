<?php
/*
 * Created on 07.09.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace xw\entities\users;

use core\utils\XWArrayList;
 
class XWUserDefinedGroupList{
	
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function addUserDefinedGroup($group){
		$this->list->add($group);
	}
	
	public function getSize(){
		return $this->list->size();
	}
	
	public function getUserDefinedGroup($index){
		return $this->list->get($index);
	}
	
	public function loadByUser($user){
		$dao=XWUserDAO::instance();
		$list=$dao->loadUserDefinedGroupListByUser($user);
		$this->list=new XWArrayList();
		for($i=0;$i<$list->getSize();$i++){
			$this->addUserDefinedGroup($list->getUserDefinedGroup($i));
		}
	}
} 
