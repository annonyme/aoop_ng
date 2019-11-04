<?php
/*
 * Created on 04.12.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2011/2012 Hannes Pries <http://www.annonyme.de>
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

use core\addons\XWAddonManager;
use core\utils\dates\XWCalendar;
use xw\entities\users\XWUser;
use core\utils\XWCodeGenerator;
use xw\entities\users\XWLoginLog;
  
class XWUserSession{
	
	private $page="";
	private $sub="";
	private $showregister="true";
	private $enableinvisible="false";
	private $onlyShowOnRequestParameter="";
	
	private $dev = true;
	
	/**
	 * @var XWAddonManager
	 */
	private $addonManager=null;
	
	private function checkSecurityNoLogin(){
		$securityNoLogin=false;
 		if($this->dev){
 			$securityNoLogin = false;
 		}
 		else if(!isset($_SESSION["XWUSER"]) && isset($_SESSION["XWFAILEDLOGINS"]) && $_SESSION["XWFAILEDLOGINS"]>=3 && isset($_SESSION["XWSECURITYNOLOGIN"])){
 			$oldCal=$_SESSION["XWSECURITYNOLOGIN"];
 			$cal=new XWCalendar();
 			if($oldCal->getTime()<$cal->getTime()){
 				unset($_SESSION["XWSECURITYNOLOGIN"]);
 				$_SESSION["XWFAILEDLOGINS"]=0;
 			}
 			else{
 				$securityNoLogin=true;
 			}
 		}
 		return $securityNoLogin;
	}
	
	public function __construct(){
		$addonManager=XWAddonManager::instance();
		if($addonManager->getAddonByName("XWServerInstanceInfos")->getInfoByName("newuseronlybyadmin")=="true"){
			$this->showregister="false";
		}
		
		// logout
		if(isset($_REQUEST["userLogout"]) && isset($_SESSION["XWUSER"])){
 			if($_SESSION["XWUSER"]->getId()==$_REQUEST["userLogout"]){
 			    $_SESSION["XWUSER"] = null;
 			    unset($_SESSION["XWUSER"]);
 			}
 		}// logout
        if(isset($_REQUEST["logout"]) && isset($_SESSION["XWUSER"])){
            $_SESSION["XWUSER"] = null;
            unset($_SESSION["XWUSER"]);
        }
		
 		//login
		if(!isset($_SESSION["XWUSER"]) || $_SESSION["XWUSER"]){
 			$securityNoLogin=$this->checkSecurityNoLogin(); 
 			if(isset($_REQUEST["username"]) && !$securityNoLogin && $this->checkSessionSecToken()){
 				$name=preg_replace("/['\";]/i","",$_REQUEST["username"]);
 				$name=trim($name);
 				//no security-actions for password.. will be hashed before inserted to sql
 				$user=new XWUser();
 				$user->login($name,$_REQUEST["userpassword"]);
 				if($user->getId()!=0){
 					$_SESSION["XWUSER"]=$user;
 					
 					if(isset($_REQUEST["userinvisible"])){
 						$_SESSION["XWUSERINVISIBLE"]=($_REQUEST["userinvisible"]=="invisible");
 					}
 					
 					if($user->isUseLoginLog()){
 						$log=new XWLoginLog();
 						$log->setIp($_SERVER["REMOTE_ADDR"]);
 						$log->setUserId($user);
 						$log->save();
 					} 					
 				}
 				else{
 					if(isset($_SESSION["XWFAILEDLOGINS"])){
 						$_SESSION["XWFAILEDLOGINS"]=$_SESSION["XWFAILEDLOGINS"]+1;
 						if($_SESSION["XWFAILEDLOGINS"]>=3){
 							$cal=new XWCalendar();
 							$cal->add($cal->MINUTE,5);
 							$_SESSION["XWSECURITYNOLOGIN"]=$cal;
 						}
 					}
 					else{
 						$_SESSION["XWFAILEDLOGINS"]=1;
 					}
 				}
 			}
 		}
 		else{
 			$addonManager=XWAddonManager::instance();
 			if($addonManager->getAddonByName("XWInstallationInfo")->getInfoByName("reloaduserbyrequest")=="true"){
 				//to refresh right/group changes without relogin
 				$_SESSION["XWUSER"]->load($_SESSION["XWUSER"]->getId());
 			}
 		}
	}
	
	public function setAddonManager($addonManager){
		$this->addonManager=$addonManager;
	}
	
	public function printForm(){ 		
 		if($this->onlyShowOnRequestParameter=="" || isset($_REQUEST[$this->onlyShowOnRequestParameter])){
	 		$securityNoLogin=$this->checkSecurityNoLogin();
	 		if($securityNoLogin){
	 				echo "<form method=\"post\">\n";
	                echo "<table id=\"loginForm\" class=\"addonPrintForm\">\n";
	                echo "  <tr>\n";
	                echo "    <td>Login-Security<br/>(retry in 5 min)</td>\n";
	                echo "  </tr>\n";
	                echo "</table>\n";
	                echo "</form>\n";
	 		}
	 		else if(!isset($_SESSION["XWUSER"])){ 			
	 				echo "<form method=\"post\">\n";
	 				$this->printHiddenInputWithSessionSecToken();
	                echo "<table id=\"loginForm\" class=\"addonPrintForm\">\n";
	                echo "  <tr>\n";
	                echo "    <td><input style=\"width:95%;\" placeholder=\"username\" name=\"username\" type=\"text\" autocomplete=\"off\"/></td>\n";
	                echo "  </tr>\n";
	                echo "  <tr>\n";
	                echo "    <td><input style=\"width:95%;\" placeholder=\"password\" name=\"userpassword\" type=\"password\"/></td>\n";
	                echo "  </tr>\n";
	                if($this->enableinvisible=="true"){
	                	echo "  <tr>\n";
	                    echo "    <td><select name=\"userinvisible\"><option value=\"visible\">visible</option><option value=\"invisible\">invisible</option></select></td>\n";
	                    echo "  </tr>\n";
	                }
	                echo "  <tr>\n";
	                echo "    <td><input class=\"submit\" type=\"submit\" value=\"Login\"/></td>\n";
	                echo "  </tr>\n";
	                if($this->showregister=="true"){
	                	$page=$this->page;
	                	$sub=$this->sub;
	                	echo "  <tr>\n";
	                	echo "    <td>[<a href=\"index.php?page=".$page."&sub=".$sub."\">register</a>]</td>\n";
	                	echo "  </tr>\n";
	                }                
	                echo "</table>\n";
	                echo "</form>\n";			
	 		}
	 		else{
	 			echo "<form method=\"post\">\n";
	 			echo "<table id=\"loginForm\" class=\"addonPrintForm\">\n";
	            echo "  <tr>\n" .
	            	 "    <td>\n";
	 			echo "User:&nbsp;<strong>".$_SESSION["XWUSER"]->getName()."</strong>";
	 			echo "    </td>\n" .
	 				 "  </tr>\n";
	 			echo "  <tr>\n";
	 			echo "    <td>\n";
	 			echo "      [<a href=\"index.php?userLogout=".$_SESSION["XWUSER"]->getId()."\">logout</a>]";
	 			echo "    </td>\n";
	 			echo "  </tr>\n";	 
	            echo "</table>\n";
	            echo "</form>\n";
	 		}
 		} 		
 	}
 	
 	public function printCurrentUserName($pre="",$after=""){
 		if(isset($_SESSION["XWUSER"])){
 			echo $pre."<span class=\"currentUserName\">".$_SESSION["XWUSER"]->getName()."</span>".$after;
 		}
 	}
 	
 	public function checkSesseionSecToken(){
 		return $this->checkSessionSecToken();
 	}
 	
 	public function checkSessionSecToken($key=null){
 		if(XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->getInfoByName("lowloginsecurity")=="true"){
 			return true;
 		}
 		
 		$result=false;
 		if($key==null){
 			if(isset($_REQUEST["_XW_SESSION_SEC"])){
 				$result=$_SESSION["XWUSER_SESSSIONSEC"]==$_REQUEST["_XW_SESSION_SEC"];
 			} 			
 		}
 		else{
 			//if(isset($_SESSION["XWUSER_SESSSIONSEC"])){
 				$result=$_SESSION["XWUSER_SESSSIONSEC"]==$key;
 			//} 			
 		}
 		
 		$gen=new XWCodeGenerator();
 		$_SESSION["XWUSER_SESSSIONSEC"]=$gen->generate(6);
 		return $result; 		
 	}
 	
 	public function printHiddenInputWithSessionSecToken(){
 		if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
 			$gen=new XWCodeGenerator();
 			$_SESSION["XWUSER_SESSSIONSEC"]=$gen->generate(6);
 		}
 		echo "<input type=\"hidden\" name=\"_XW_SESSION_SEC\" value=\"".$_SESSION["XWUSER_SESSSIONSEC"]."\"/>";
 	}
 	
 	public function getSessionSecToken(){
 		if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
 			$gen=new XWCodeGenerator();
 			$_SESSION["XWUSER_SESSSIONSEC"]=$gen->generate(6);
 		}
 		return $_SESSION["XWUSER_SESSSIONSEC"];
 	}
 	
 	public function getURLParameterWithSessionSecToken(){
 		if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
 			$gen=new XWCodeGenerator();
 			$_SESSION["XWUSER_SESSSIONSEC"]=$gen->generate(6);
 		}
 		return "_XW_SESSION_SEC=".$_SESSION["XWUSER_SESSSIONSEC"]."";
 	}
 	
 	public function getURLParameterWithSessionSecTokenValueOnly(){
 		if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
 			$gen=new XWCodeGenerator();
 			$_SESSION["XWUSER_SESSSIONSEC"]=$gen->generate(6);
 		}
 		return $_SESSION["XWUSER_SESSSIONSEC"]."";
 	}
 	
 	public function setPage($page){
 		$this->page=$page;
 	}
 	
 	public function getPage(){
 		return $this->page;
 	} 
 	
 	public function setSub($sub){
 		$this->sub=$sub;
 	}
 	
 	public function getSub(){
 		return $this->sub;
 	}
 	
 	public function setShowregister($showregister){
 		$this->showregister=$showregister;
 	}
 	
 	public function getShowregister(){
 		return $this->showregister;
 	}
 	
 	public function setEnableinvisible($enableinvisible){
 		$this->enableinvisible=$enableinvisible;
 	}
 	
 	public function getEnableinvisible(){
 		return $this->enableinvisible;
 	}
 	
 	public function setOnlyShowOnRequestParameter($onlyShowOnRequestParameter){
 		$this->onlyShowOnRequestParameter=$onlyShowOnRequestParameter;
 	}
 	
 	public function getOnlyShowOnRequestParameter(){
 		return $this->onlyShowOnRequestParameter;
 	}
} 
