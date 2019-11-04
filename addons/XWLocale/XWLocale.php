<?php
/*
 * Created on 15.10.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\utils\XWLocaleResolver;
 
class XWLocale{
	
	private $defaultLocale="EN";
	private $tryreadbrowserlocale="true";
	private $addonManager=null;
	
	public function __construct(){
		
	}
	
	public function setAddonManager($addonManager){
		$this->addonManager=$addonManager;
	}
	
	public function getDefaultLocale(){
		return $this->defaultLocale;
	}
	
	public function setDefaultLocale($defaultLocale){
		$this->defaultLocale=$defaultLocale;
	}
	
	public function getTryreadbrowserlocale(){
		return $this->tryreadbrowserlocale;
	}
	
	public function setTryreadbrowserlocale($tryreadbrowserlocale){
		$this->tryreadbrowserlocale=$tryreadbrowserlocale;
	}
	
	public function getCurrentLocale(){
		return $this->findLocale();
	}
	
	public function findLocale(){		
		if(isset($_SESSION["XWLOCALE"]) && !isset($_REQUEST["resetlocale"])){
			return $_SESSION["XWLOCALE"];
		}
		else{
			
			$loc="";
			if($this->tryreadbrowserlocale=="true"){
				$localeRes=new XWLocaleResolver();
				$loc=$localeRes->getLocaleString();				
			}
			
			if($loc!=""){
				$_SESSION["XWLOCALE"]=$loc;
				//properties reader will fallback to default if locale is not found
			}
			else{
				$addonManager=$this->addonManager;
				if($addonManager->getAddonByName("XWServerInstanceInfos")->existsInfo("locale")){
					$locale=$addonManager->getAddonByName("XWServerInstanceInfos")->getInfoByName("locale");
					if($locale!=""){
						$_SESSION["XWLOCALE"]=strtoupper($locale);
					}
					else{
						$_SESSION["XWLOCALE"]=strtoupper($this->defaultLocale);
					}
				}
				else{
					$_SESSION["XWLOCALE"]=strtoupper($this->defaultLocale);
				}
			}			
			
			return strtoupper($_SESSION["XWLOCALE"]);
		}
	}
} 
