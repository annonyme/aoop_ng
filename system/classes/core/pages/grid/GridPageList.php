<?php
namespace core\pages\grid;

use core\utils\XWArrayList;
use DirectoryIterator;
use Exception;
use core\pages\PageListInterface;

class GridPageList implements PageListInterface{
	private $list=null;
	
	public function __construct(){
		$this->list=new XWArrayList();
	}
	
	public function loadByFolder($folder){
		$this->list->clear();
		try{
			$di=new DirectoryIterator($folder);
			foreach($di as $file){
				if($file->isFile() && preg_match("/\.grid\.json$/i",$file->getFilename())){
					$page = new GridPage();
					$page->load($folder,preg_replace("/\.grid\.json$/i","",$file->getFilename()));
					if($page->getCallName() != ""){
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
	
	/**
	 * @return int
	 */
	public function getSize(){
		return $this->list->size();
	}
	
	/**
	 * @return GridPage
	 * @param int $index
	 */
	public function getPage($index){
		return $this->list->get($index);
	}
	
	/**
	 * @return GridPage
	 * @param string $name
	 */
	public function getPageByName($name){
		$page=new GridPage();
		for($i=0;$i<$this->getSize();$i++){
			$dummy=$this->getPage($i);
			if($dummy->getCallName()==$name){
				$page=$dummy;
				return $page;
			}
		}
		return $page;
	}
	
	/**
	 * @return bool
	 * @param GridPage $page
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
	
	/**
	 * @return XWArrayList
	 */
	public function getAsList(){
		return $this->list;
	}
	/**
	 * {@inheritDoc}
	 * @see \core\pages\PageListInterface::getPageByAlias()
	 */
	public function getPageByAlias($alias, $locale = null) {
		return $this->getPageByName($alias);
	}
}