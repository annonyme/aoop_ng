<?php
namespace core\pages\plain;
/*
 * Created on 01.08.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\pages\PageListFactoryInterface;
use core\utils\XWServerInstanceToolKit;
use Exception;
 
class XWPageListFactory implements PageListFactoryInterface{
	/** @var XWPageList */
    static private $pageList=null;

    /**
     * @param null|string $pageDir
     * @return XWPageList
     */
	static public function getFullPageList($pageDir = null){
		if(self::$pageList==null){
			try{
                if ($pageDir === null) {
                    $pageDir = XWServerInstanceToolKit::instance()->getServerSwitch()->getPages();
                }
                $pages = new XWPageList();
                $pages->loadByFolder($pageDir);
                $pages->sortByNameASC();

                self::$pageList = $pages;
			}
			catch(Exception $e){
				echo $e;
			}
		}
		return self::$pageList;
	}
}
