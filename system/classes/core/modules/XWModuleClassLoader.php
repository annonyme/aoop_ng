<?php
namespace core\modules;

use core\modules\factories\XWModuleListFactory;
use core\utils\config\GlobalConfig;

class XWModuleClassLoader{
	public static function addPsr4($autoloader){
		$list = XWModuleListFactory::getFullModuleList(GlobalConfig::instance()->getValue("modulesfolder"));
		$deployFolder = GlobalConfig::instance()->getValue("moduledeployfolder");
		$paths = [];
		for($i=0;$i<$list->getSize();$i++){
			$module=$list->getModule($i);
			if(is_dir($module->getPath()."/".$deployFolder."classes/")){
				$paths[] = $module->getPath()."/".$deployFolder."classes/";
			}			
		}
		$autoloader->addPsr4("", $paths);
	}
}