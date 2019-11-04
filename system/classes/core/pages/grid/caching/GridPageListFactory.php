<?php
namespace core\pages\caching;

use core\pages\grid\GridPageList;
use Exception;
use core\pages\PageListFactoryInterface;

class GridPageListFactory implements PageListFactoryInterface{
	public function __construct(){
	
	}	
	static private $pageList=[];
	
	/**
	 * @return GridPageList
	 * @param string $pageDir
	 */
	static public function getFullPageList($pageDir = null){
		if(!isset(self::$pageList[$pageDir])){
			try{
				$pages=new GridPageList();
				$pages->loadByFolder($pageDir);
				self::$pageList[$pageDir]=$pages;
			}
			catch(Exception $e){
				echo $e;
			}
		}
		return self::$pageList[$pageDir];
	}
}