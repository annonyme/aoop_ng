<?php
/*
 * Created on 02.08.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007 Hannes Pries <http://www.annonyme.de>
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

namespace core\menu\admin;

use core\modules\factories\XWModuleListFactory;
use DirectoryIterator;
use xw\entities\users\XWUser;

class XWAdminMenuBuilder{
 	private $fileContentList=[];
 	
 	public function __construct(){
 		
 	}
 	
 	public function buildFromPath($path="menuElements/"){
 		$this->fileContentList=[];
 		if(is_dir($path)){
 			$di = new DirectoryIterator($path);
 			foreach($di as $file){
 				if($file->isFile()){
 					$content=file_get_contents($file->getPath());
 					$content=preg_replace("/<\?php.+\?>/Uis","",$content);
 					$this->addMenuFileContent($content);
 				}
 			}
 			return true;
 		}
 		else{
 			return false;
 		}
 	}
 	
 	public function buildFromModules(XWUser $user){
 		$this->fileContentList=[];
 		
 		$modules=XWModuleListFactory::getFullModuleList();
		
		for($i=0;$i<$modules->getSize();$i++){
			$module=$modules->getModule($i);
			if($module->getCallName() != "system" && ($module->getAdminGroup()=="" || $user->isInGroup("admins") || $user->isInGroup($module->getAdminGroup()))){
				if(file_exists($module->getPath()."/deploy/admin/index.php")){
					$content=file_get_contents($module->getPath()."/deploy/admin/index.php");
					$content=preg_replace("/_moduleName_/",$module->getName(),$content);
					$content=preg_replace("/_moduleCallName_/",$module->getCallName(),$content);
					$content=preg_replace("/_moduleVersion_/",$module->getVersion(),$content);
					$content=preg_replace("/<\?php.+\?>/Uis","",$content);
	 				$this->addMenuFileContent($content);
				}
			}			
		}
 	}
 	
 	public function addMenuFileContent($content){
 		$this->fileContentList[count($this->fileContentList)]=$content;
 	}
 	
 	public function getSize(){
 		return count($this->fileContentList);
 	}
 	
 	public function getMenuFileContent($index){
 		return $this->fileContentList[$index];
 	}
 }
?>
