<?php
/*
 * Created on 18.09.2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
 * Copyright (c) 2013/2017 Hannes Pries <http://www.annonyme.de>
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

namespace core\modules\factories;

use core\modules\XWModuleList;
use core\utils\XWServerInstanceToolKit;
use core\utils\config\GlobalConfig;
use Exception;

class XWModuleListFactory{
    private static $moduleList=null;
	
	/**
	 * @return XWModuleList
	 * @param string|null $globalDir
	 * @param string|null $pageDir
	 */
	public static function getFullModuleList($globalDir = null, $pageDir = null){
		if(self::$moduleList==null){
			try{
				if(isset($_SESSION["XW_MODULE_LIST_FULL_CACHING"]) && $_SESSION["XW_MODULE_LIST_FULL_CACHING_REFRESH"]<25){
					self::$moduleList=$_SESSION["XW_MODULE_LIST_FULL_CACHING"];
					$_SESSION["XW_MODULE_LIST_FULL_CACHING_REFRESH"]++;					
				}
				else{
					$modules=new XWModuleList();
					
					//GLOBAL dir
					//load modules from global module-folder, even if no custom-descriptor-file in the instance module
					//folder exists		
					if($globalDir === null){
					    $globalDir = XWServerInstanceToolKit::instance()->getServerSwitch()->getGlobalModuleDir();
					}
					if(GlobalConfig::instance()->getValue("loadGlobalModulesWithoutCustomDescriptor", "false") == "true"){
					    $modules->load($globalDir, "");
					}
					
					//PAGE dir					
					if($pageDir === null){
					    $pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath()."modules/";
					}
					
					if($pageDir !== null && strlen($pageDir) > 0 && is_dir($pageDir)){
						$pageModules=new XWModuleList();
						$pageModules->load($pageDir, $globalDir !== null ? $globalDir : "");
							
						for($i=0;$i<$pageModules->getSize();$i++){
							$modules->addModule($pageModules->getModule($i));
						}
					}
					
					$modules->sortByName();
					$_SESSION["XW_MODULE_LIST_FULL_CACHING"]=$modules;
					$_SESSION["XW_MODULE_LIST_FULL_CACHING_REFRESH"]=0;
					self::$moduleList = $modules;
					//echo "<!-- refresh module list -->";
				}
			}
			catch(Exception $e){
				echo $e;
			}
		}
		return self::$moduleList;
	}
	
	public static function reset(){
		$_SESSION["XW_MODULE_LIST_REFRESH"]=30;
		self::$moduleList=null;
	}
} 
