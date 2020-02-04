<?php
namespace addons\system;

/*
 * Created on 01.12.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2018 Hannes Pries <https://www.hannespries.de>
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

use core\addons\XWAddonImplementation;
use core\addons\XWAddonManager;
use core\utils\XWServerInstanceToolKit;
use XWServerInstanceInfos;

class XWPageEditButton extends XWAddonImplementation {
	
	private $editPage="editPage";
	private $editIcon="edit.png";
	
	public function __construct(){
		
	}
	
	public function render($vars = []):string {
		$result = '';
	    $addonManager=XWAddonManager::instance();
		if(isset($_SESSION["XWUSER"]) && !isset($_REQUEST["adminpage"])){
			$instanceAdmins="";
			/** @var XWServerInstanceInfos $serverInstance */
			$serverInstance = $addonManager->getAddonByName("XWServerInstanceInfos");
			if($serverInstance->existsInfo("admins")){
				$instanceAdmins=$serverInstance->getInfoByName("admins");
			}
			if($_SESSION["XWUSER"]->isInGroup($instanceAdmins) || $_SESSION["XWUSER"]->isInGroup("admins")){
				if($_SESSION["XWUSER"]->isInGroup("pageAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){
					$page=$_REQUEST["page"] ?? $serverInstance->getInfoByName("homepage");
					
					$pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
					
					$locale="";
			        if($addonManager->getAddonByName("XWLocale")){
			            $locale=$addonManager->getAddonByName("XWLocale")->findLocale();
			        }
			        $locale=strtolower($locale);
					
					if(file_exists($pageDir.$page.".html")){
						$result .= "<div id=\"XWPageEditButtonContainer\" class=\"pageEditButtonStyle\">\n";
                        $result .= "<a href=\"index.php?adminpage=1&page=".$this->editPage."&pageName=".$page."&locale=".$locale."\"><img src=\"images/".$this->editIcon."\" alt=\"edit\"/> edit '".$page."'</a>";
                        $result .= "</div>\n";
					}
				}
			}		
		}
		return $result;
	}
	
	public function getEditPage(){
		return $this->editPage;
	}
	
	public function setEditPage($editPage){
		$this->editPage=$editPage;
	}
	
	public function getEditIcon(){
		return $this->editIcon;
	}
	
	public function setEditIcon($editIcon){
		$this->editIcon=$editIcon;
	}
} 
