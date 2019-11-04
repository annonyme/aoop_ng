<?php
namespace core\pages\grid;

use core\utils\XWArrayList;
use DirectoryIterator;

class GridPageModuleDescriptionList{
	private $list = null;
	
	public function __construct(){
		$this->list = new XWArrayList();
	}
	
	public function loadGlobalModuleDescription($pagesFolder){
		$this->list->clear();
		try{
			$di=new DirectoryIterator($pagesFolder."/pagemodules");
			foreach($di as $file){
				if($file->isFile() && preg_match("/\.grid\.module\.json$/i",$file->getFilename())){
					$description = new GridPageModuleDescription(null);
					$description->load($file->getPathname());
					if($description->getCallName() != "" && $description->getScope() == "global"){
						$this->addModuleDescription($description);
					}
				}
			}
		}
		catch(\Exception $e){
		
		}
	}
	
	public function addModuleDescription($moduleDescription){
		$this->list->add($moduleDescription);
	}
	
	/**
	 * @return int
	 */
	public function getSize(){
		return $this->list->size();
	}
	
	/**
	 * @return GridPageModuleDescription
	 * @param int $index
	 */
	public function getModuleDescription($index){
		return $this->list->get($index);
	}
	
	/**
	 * @return GridPageModuleDescription
	 * @param string $name
	 */
	public function getPageById($id){
		$mod = new GridPageModuleDescription();
		for($i = 0; $i < $this->getSize(); $i++){
			$dummy = $this->getModuleDescription($i);
			if($dummy->getId() == $id){
				$mod = $dummy;
				return $mod;
			}
		}
		return $mod;
	}
	
	/**
	 * @return bool
	 * @param GridPageModuleDescription $mod
	 */
	public function existsIn($mod){
		$found=false;
		$size=$this->getSize();
		for($i=0;$i<$size;$i++){
		$dummy = $this->getModuleDescription($i);
			if($dummy->getId() == $mod->getId()){
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
}