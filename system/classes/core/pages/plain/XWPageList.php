<?php
namespace core\pages\plain;

use core\pages\PageListInterface;
use core\utils\XWArrayList;
use DirectoryIterator;
use Exception;

/*
 * Created on 31.07.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class XWPageList implements PageListInterface{
	
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function loadByFolder($folder){
		$this->list->clear();
 		try{        
	        $di=new DirectoryIterator($folder);
	        foreach($di as $file){
	        	if($file->isFile() && preg_match("/\.html$/i",$file->getFilename())){
	        		$page=new XWPage();
	        		$page->load(preg_replace("/\.html$/i","",$file->getFilename()), $folder, null);
	        		if($page->getCallName()!=""){
	        			$this->addPage($page);
	        		}
	        	}
	        }
 		}
 		catch(Exception $e){
 			
 		}        
	}
	
	public function addPage($page){
		$this->list->add($page);
	}
	
	public function getSize(){
		return $this->list->size();
	}
	
	/**
	 * @return XWPage
	 * @param int $index
	 */
	public function getPage($index){
		return $this->list->get($index);
	}
	
	public function getPageByName($name){
		$page=new XWPage();
		for($i=0;$i<$this->getSize();$i++){
			$dummy=$this->getPage($i);
			if($dummy->getCallName()==$name){
                $page=$dummy;
				return $page;
			}
			else if($dummy->getParent() && $dummy->getParent(). '/'. $dummy->getCallName() == $name){
				$page=$dummy;
				return $page;
			}
		}
		return $page;
	}

	/**
	 * @param XWPage $page
	 *
	 * @return bool
	 */
	public function existsIn($page){
		$found=false;
		$size=$this->getSize();
		for($i=0;$i<$size;$i++){
			$dummy=$this->getPage($i);
			if($dummy->getCallName()==$page->getCallName()){
				$found=true;
				return $found;
			}
		}
		return $found;
	}
	
	public function getAsList(){
		return $this->list;
	}
	
	public function getPageByAlias($alias, $locale = null) {
		return $this->getPageByName($alias);	
	}

	public function sortByNameASC(){
		$list = $this->list->toArray();
		uasort($list, function($a, $b){
			/** @var XWPage $a */
            /** @var XWPage $b */
            if($a->getOrderIndex() > $b->getOrderIndex()){
            	return 1;
			}
			else if($a->getOrderIndex() < $b->getOrderIndex()){
            	return -1;
			}
			return strcmp($a->getName(), $b->getName());
		});
        $this->list=new XWArrayList($list);
	}
}
