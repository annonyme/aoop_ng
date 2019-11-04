<?php
/*
 * Created on 18.09.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\addons\XWAddonManager;
use core\modules\XWModuleDeployer;
use core\modules\XWModule;
use core\modules\factories\XWModuleListFactory;
use core\utils\XWServerInstanceToolKit;
use core\utils\XWArrayList;
use xw\entities\users\XWUser;
use core\pages\plain\XWPage;
use core\utils\XWLocalePropertiesReader;
 
class XWExtendMenu{
	
	private $addonManager=null;
	private $pageDir="";
	private $homepage="";
	private $adminGroup="";
	
	private $pageFactoryClassName = "core\pages\plain\XWPageListFactory";
	
	public function __construct(){
     	$this->pageDir=XWServerInstanceToolKit::instance()->getServerSwitch()->getPages();
     	$this->homepage=XWServerInstanceToolKit::instance()->getServerSwitch()->getHomepage();
     	$this->adminGroup=XWServerInstanceToolKit::instance()->getServerSwitch()->getAdmins();
	}
	
	private function getPageFolder(){
		return XWServerInstanceToolKit::instance()->getServerSwitch()->getPages();
	}
	
	private function getFullPageList(){
		$result = null;
		$ref = new ReflectionClass($this->pageFactoryClassName);
		if($ref->hasMethod("getFullPageList")){
			$method = $ref->getMethod("getFullPageList");
			$result = $method->invokeArgs(null, [$this->getPageFolder()]);
		}
		return $result;
	}
	
	public function setAddonManager($addonManager){
		$this->addonManager=$addonManager;
	}
	
	private function getPageLinkListItem(XWPage $page, $class = "", $upperFirst = true){
	    $link = "index.php?page=".$page->getCallName();
	    if(strlen($page->getLink()) > 0){
	        $link = $page->getLink();
	    }
	    if($upperFirst){
	        return "<li><a href=\"".$link."\" class=\"".$class."\">".ucfirst($page->getName())."</a></li>\n";
	    }
	    else{
	        return "<li><a href=\"".$link."\" class=\"".$class."\">".$page->getName()."</a></li>\n";
	    }
	}
	
	public function printPagesMenu($parentPage="",$pages=null,$request=null,$upperFirst=true){
     	$homepage=$this->homepage;

     	if($request==null){
         	$request=$_REQUEST;
     	}

     	if(!isset($request["page"]) && !isset($request["adminpage"])){
         	$request["page"]=$homepage;         	
     	}
        
     	if($pages==null){
     		$pageList=$this->getFullPageList();
     		$pages=$pageList->getAsList();
     	}
     	
     	$selectedPages=new XWArrayList();
     	for($i=0;$i<$pages->size();$i++){
     		$page=$pages->get($i);
     		if(!$page->isHidden() && $page->getName()!="" && $page->getParentPage()==$parentPage){
     			$selectedPages->add($page);
     		}
     	}     	

     	if($selectedPages->size()>0){
     		echo "<nav><ul class=\"extendModulePages nav navbar-nav\">\n";
     		for($i=0;$i<$selectedPages->size();$i++){
     			/** @var XWPage $outPage */
     		    $outPage=$selectedPages->get($i);
                $class="menu";
                if(isset($request["page"]) && $outPage->getCallName()==$request["page"] && !isset($request["adminpage"])){
            		$class.=" menuCur active";            	
            	}             	
            	
            	echo $this->getPageLinkListItem($outPage, $class, $upperFirst);
                
                $this->printPagesMenu($outPage->getCallName(),$pages,$request,$upperFirst);
     		}     		
     		echo "</ul></nav>\n";
     	}     	
	}
	
	public function printPagesMenuSimpleList($parentPage="",$pages=null,$request=null,$upperFirst=true){
		$homepage=$this->homepage;
	
		if($request==null){
			$request=$_REQUEST;
		}
	
		if(!isset($request["page"]) && !isset($request["adminpage"])){
			$request["page"]=$homepage;
		}
	
		if($pages==null){
			$pageList=$this->getFullPageList();
			$pages=$pageList->getAsList();
		}
	
		$selectedPages=new XWArrayList();
		for($i=0;$i<$pages->size();$i++){
			$page=$pages->get($i);
			if(!$page->isHidden() && $page->getName()!="" && $page->getParentPage()==$parentPage){
				$selectedPages->add($page);
			}
		}
	
		if($selectedPages->size()>0){
			echo "<nav><ul class=\"extendModulePages\">\n";
			for($i=0;$i<$selectedPages->size();$i++){
				$outPage=$selectedPages->get($i);
				$class="menu";
				if(isset($request["page"]) && $outPage->getCallName()==$request["page"] && !isset($request["adminpage"])){
					$class.=" menuCur active";
				}
				 
				echo $this->getPageLinkListItem($outPage, $class, $upperFirst);
	
				$this->printPagesMenuSimpleList($outPage->getCallName(),$pages,$request,$upperFirst);
			}
			echo "</ul></nav>\n";
		}
	}
	
	//@deprecated
	public function printPagesOnlyMenu($seperator="|",$request=null,$upperFirst=true){
		$pageDir=$this->pageDir;
     	$homepage=$this->homepage;

     	if($request==null){
         	$request=$_REQUEST;
     	}

     	if(!isset($request["page"])){
         	if(!isset($request["adminpage"])){
         		$request["page"]=$homepage;
         	}        	
     	}
     	
     	$menu=new XWArrayList();
     	$fileName="";
     	$page=null;
     	
     	$di=new DirectoryIterator($pageDir);
     	foreach($di as $file){
     		if(!$file->isDot() && !$file->isDir()){
     			//only add index pages of multipages....
     			$fileName=preg_replace("/\.html$/i","",$file->getFilename());
     			$page=new XWPage();
     			$page->load($fileName,$pageDir);
     			if(!$page->isHidden() && $page->getName()!=""){
     				if(!$page->isMultiPage() || $page->isInitPage()){
     					$menu->add($page);
     				}
     			}
     		}
     	}
     	
     	$outPage=null;
     	echo "<nav><ul class=\"extendModulePages nav navbar-nav\">\n";
        for($i=0;$i<$menu->size();$i++){
            $outPage=$menu->get($i);
            $class="menu";
            
            if(isset($request["page"]) && $outPage->getCallName()==$request["page"] && !isset($request["adminpage"])){
            	$class.=" menuCur active";            	
            }             
            
            if($i<($menu->size()-1)){
                echo $this->getPageLinkListItem($outPage, $class, $upperFirst) . $seperator;
            }
            else{
                echo $this->getPageLinkListItem($outPage, $class, $upperFirst);
            }   	
        }
        echo "</ul></nav>\n";
	}
	
	public function printAdminPanelLink($request=null){
		$adminGroup=$this->adminGroup;
 	    if(isset($_SESSION["XWUSER"]) && ($_SESSION["XWUSER"]->isInGroup($adminGroup) || $_SESSION["XWUSER"]->isInGroup("admins"))){
 	 	 	if(isset($request["adminpage"])){
         	 	echo "<a href=\"index.php?page=index&adminpage=1\" class=\"menu menuCur active\">admin</a>";
         	}
         	else{
         	 	echo "<a href=\"index.php?page=index&adminpage=1\" class=\"menu\">admin</a>";
         	}
 	 	} 
	}
	
	public function printAdminPanelLinkAsList($request=null){
     	$adminGroup=$this->adminGroup;
 	    if(isset($_SESSION["XWUSER"]) && ($_SESSION["XWUSER"]->isInGroup($adminGroup) || $_SESSION["XWUSER"]->isInGroup("admins"))){
 	 	 	if(isset($request["adminpage"])){
         	 	echo "<nav><ul class=\"nav navbar-nav\"><li><a href=\"index.php?page=index&adminpage=1\" class=\"menu menuCur active\">admin</a></li></ul></nav>\n";
         	}
         	else{
         	 	echo "<nav><ul class=\"nav navbar-nav\"><li><a href=\"index.php?page=index&adminpage=1\" class=\"menu\">admin</a></li></ul></nav>\n";
         	}
 	 	} 
	}
	
	public function printAdminPanelLinkAsSimpleList($request=null){
	    $adminGroup=$this->adminGroup;
	    if(isset($_SESSION["XWUSER"]) && ($_SESSION["XWUSER"]->isInGroup($adminGroup) || $_SESSION["XWUSER"]->isInGroup("admins"))){
	        if(isset($request["adminpage"])){
	            echo "<nav><ul class=\"\"><li><a href=\"index.php?page=index&adminpage=1\" class=\"menu menuCur active\">admin</a></li></ul></nav>\n";
	        }
	        else{
	            echo "<nav><ul class=\"\"><li><a href=\"index.php?page=index&adminpage=1\" class=\"menu\">admin</a></li></ul></nav>\n";
	        }
	    }
	}
	
	public function printModulesOnlyMenuAsSimpleList($seperator="|",$request=null,$noSubMenu=false,$loadPagesDirAlso=false,$upperFirst=true){
	    $homepage=$this->homepage;
	    
	    $addonManager=$this->addonManager;
	    
	    if($request==null){
	        $request=$_REQUEST;
	    }
	    
	    if(!isset($request["page"])){
	        if(!isset($request["adminpage"])){
	            $request["page"]=$homepage;
	        }
	    }
	    
	    $modules=XWModuleListFactory::getFullModuleList();
	    
	    $module=null;
	    $user=new XWUser();
	    if(isset($_SESSION["XWUSER"])){
	        $user=$_SESSION["XWUSER"];
	    }
	    echo "<nav><ul class=\"extendModulePages\">\n";
	    $moduleCount=$modules->getSize();
	    for($i=0;$i<$moduleCount;$i++){
	        $module=$modules->getModule($i);
	        
	        if($module->hasUserPermission($user)){
	            $trans=new XWLocalePropertiesReader();
	            //adds translation due dictionary-lib
	            if(!$addonManager->getAddonByName("XWDictionaries")->existsIn($module->getCallName())){
	                $trans->importPropertiesBundle($module->getDictionaryPath(),$addonManager->getAddonByName("XWLocale")->findLocale());
	                $addonManager->getAddonByName("XWDictionaries")->addDictionary($module->getCallName(),$trans);
	            }
	            
	            $printSeperator=false;
	            
	            if(isset($request["page"]) && $module->getCallName()==$request["page"]){
	                if(!$module->isHidden()){
	                    echo "<li>\n";
	                    if($upperFirst){
	                        echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu menuCur active\">".ucfirst($trans->getEntry($module->getName()))."</a>\n";
	                        $printSeperator=true;
	                    }
	                    else{
	                        echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu menuCur active\">".$trans->getEntry($module->getName())."</a>\n";
	                        $printSeperator=true;
	                    }
	                    if(!$noSubMenu){
	                        $this->printModuleMenuForCurrentModuleAsList($request,false, "", false);
	                    }
	                    echo "</li>\n";
	                }
	            }
	            else{
	                if(!$module->isHidden()){
	                    echo "<li>\n";
	                    if($upperFirst){
	                        echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu\">".ucfirst($trans->getEntry($module->getName()))."</a>\n";
	                        $printSeperator=true;
	                    }
	                    else{
	                        echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu\">".$trans->getEntry($module->getName())."</a>\n";
	                        $printSeperator=true;
	                    }
	                    echo "</li>\n";
	                }
	            }
	            
	            if($printSeperator && $i<($modules->getSize()-1)){
	                echo $seperator;
	            }
	        }
	    }
	    echo "</ul></nav>\n";
	}
	
	public function printModulesOnlyMenu($seperator="|",$request=null,$noSubMenu=false,$loadPagesDirAlso=false,$upperFirst=true){
     	$homepage=$this->homepage;
     	
     	$addonManager=$this->addonManager;

     	if($request==null){
         	$request=$_REQUEST;
     	}
     	
     	if(!isset($request["page"])){
         	if(!isset($request["adminpage"])){
         		$request["page"]=$homepage;
         	}        	
     	}
		
		$modules=XWModuleListFactory::getFullModuleList();	
		
		$module=null;
		$user=new XWUser();
		if(isset($_SESSION["XWUSER"])){
			$user=$_SESSION["XWUSER"];
		}
		echo "<nav><ul class=\"extendModulePages nav navbar-nav\">\n";
		$moduleCount=$modules->getSize();
		for($i=0;$i<$moduleCount;$i++){
			$module=$modules->getModule($i);						
			
			if($module->hasUserPermission($user)){
				$trans=new XWLocalePropertiesReader();
				//adds translation due dictionary-lib
				if(!$addonManager->getAddonByName("XWDictionaries")->existsIn($module->getCallName())){
					$trans->importPropertiesBundle($module->getDictionaryPath(),$addonManager->getAddonByName("XWLocale")->findLocale());
					$addonManager->getAddonByName("XWDictionaries")->addDictionary($module->getCallName(),$trans);
				}
				
				$printSeperator=false;
				
				if(isset($request["page"]) && $module->getCallName()==$request["page"]){	
					if(!$module->isHidden()){
						echo "<li>\n";
						if($upperFirst){
							echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu menuCur active\">".ucfirst($trans->getEntry($module->getName()))."</a>\n";
							$printSeperator=true;
						}
						else{
							echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu menuCur active\">".$trans->getEntry($module->getName())."</a>\n";
							$printSeperator=true;
						}					
						if(!$noSubMenu){					
            				$this->printModuleMenuForCurrentModuleAsList($request,false);
						}
						echo "</li>\n";
					}
				}
				else{
					if(!$module->isHidden()){
						echo "<li>\n";
						if($upperFirst){
							echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu\">".ucfirst($trans->getEntry($module->getName()))."</a>\n";
							$printSeperator=true;
						}
						else{
							echo "<a href=\"index.php?page=".$module->getCallName()."\" class=\"menu\">".$trans->getEntry($module->getName())."</a>\n";
							$printSeperator=true;
						}
						echo "</li>\n";					
					}				
				}
				
				if($printSeperator && $i<($modules->getSize()-1)){
					echo $seperator;
				}
			}			
		}
		echo "</ul></nav>\n";
	}
	
	public function printModuleMenuForCurrentModule($seperator="|",$request=null,$onlyIfHidden=true){
     	$homepage=$this->homepage;
     	$addonManager=$this->addonManager;

     	if($request==null){
         	$request=$_REQUEST;
     	}
     	
     	if(!isset($request["page"])){
         	if(!isset($request["adminpage"])){
         		$request["page"]=$homepage;
         	}        	
     	}
		
		$modules=XWModuleListFactory::getFullModuleList();
		
		$user=new XWUser();
		if(isset($_SESSION["XWUSER"])){
			$user=$_SESSION["XWUSER"];
		}
		
		$module=null;
		if(isset($request["page"])){
			$module=$modules->getModuleByCallName($request["page"]);
		}		
		if($module!=null && $module->getCallName()!="" && $module->hasUserPermission($user)){
			$print=true;
			if($onlyIfHidden){
				if(!$module->isHidden()){
					$print=false;
				}
			}
			
			$trans=$addonManager->getAddonByName("XWDictionaries")->getDictionary($module->getCallName());
			$addonManager->getAddonByName("XWDictionaries")->addDictionary($module->getCallName(),$trans);
			
			if($print){
				$deployer=new XWModuleDeployer();
         		$deployer->load($module);
         		
         		$this->printItemsFlat($deployer,$module,$request,$seperator);
			}
		}
	}
	
	public function printModuleMenuForCurrentModuleAsList($request=null,$onlyIfHidden=true,$specificModuleName="", $useBootstrap = true){
     	$homepage=$this->homepage;

     	if($request==null){
         	$request=$_REQUEST;
     	}
     	
     	if(!isset($request["page"])){
         	if(!isset($request["adminpage"])){
         		$request["page"]=$homepage;
         	}        	
     	}
     	
     	$requestModuleName=$request["page"];
		if($specificModuleName!=""){
			$requestModuleName=$specificModuleName;
		}		
		
		$modules=XWModuleListFactory::getFullModuleList();	
		$modules->sortByName();
		$module=$modules->getModuleByCallName($requestModuleName);
		
		$this->printMenuOfModuleAsList($module,$request,$onlyIfHidden, $useBootstrap);
	}
	
	private function printMenuOfModuleAsList($module,$request,$onlyIfHidden=true, $useBootstrap = true){
		$user=new XWUser();
		if(isset($_SESSION["XWUSER"])){
			$user=$_SESSION["XWUSER"];
		}
		if($module!=null && $module->getCallName()!="" && $module->hasUserPermission($user)){
			$print=true;
			if($onlyIfHidden){
				if(!$module->isHidden()){
					$print=false;
				}
			}
			
			if($print){
				$deployer=new XWModuleDeployer();
         		$deployer->load($module);
         	
         		$this->printItems($deployer,$module,$request, $useBootstrap);         		
			}
		}
	}
	
	private function printItems($itemContainer,$module,$request, $useBootstrap = true){
		$addonManager=$this->addonManager;
		$trans=$addonManager->getAddonByName("XWDictionaries")->getDictionary($module->getCallName());
		$item=null;
		if($useBootstrap){
		    echo "<ul class=\"extendModulePages nav navbar-nav\">\n";
		}
		else{
		    echo "<ul class=\"extendModulePages\">\n";
		}        
        $contSize=$itemContainer->getSize();
        for($j=0;$j<$contSize;$j++){
            $item=$itemContainer->getSubPageMenuItem($j);                
            $class="pageMenuLink";
            $href="index.php?page=".$module->getCallName()."&sub=".$item->getLinkedPage()."";
            if($item->isOnlyVisibleWithLogin() && !isset($_SESSION["XWUSER"])){
                $class="pageMenuLinkInActive";
                $href="#";
            }
            if(isset($request["sub"]) && ($item->getLinkedPage()==$request["sub"] || (!isset($request["sub"]) && $item->getLinkedPage()=="index"))){
                $class="pageMenuLinkCurrent active";
            }
            $icon="";
            if($item->getIcon()!=""){
                $icon="<img src=\"images/".$item->getIcon()."\" alt=\"menuItemIcon\" class=\"menuItemIcon\"/>";
            }
                
            echo "<li><a href=\"".$href."\" class=\"".$class."\">".$icon." ".$trans->getEntry($item->getLabel())."</a>";
            if($item->getSize()>0){
            	$this->printItems($item,$module,$request, $useBootstrap);
            }
            echo "</li>\n";                	
        }
        echo "</ul>\n";
	}
	
	/**
	 * 
	 */
	private function printItemsFlat($itemContainer,$module,$request,$seperator){
		$addonManager=$this->addonManager;
		$trans=$addonManager->getAddonByName("XWDictionaries")->getDictionary($module->getCallName());
		$item=null;
        
        $contSize=$itemContainer->getSize();
        for($j=0;$j<$contSize;$j++){
            $item=$itemContainer->getSubPageMenuItem($j);                
            $class="pageMenuLink";
            $href="index.php?page=".$module->getCallName()."&sub=".$item->getLinkedPage()."";
            if($item->isOnlyVisibleWithLogin() && !isset($_SESSION["XWUSER"])){
                $class="pageMenuLinkInActive";
                $href="#";
            }
            if(isset($request["sub"]) && ($item->getLinkedPage()==$request["sub"] || (!isset($request["sub"]) && $item->getLinkedPage()=="index"))){
                $class="pageMenuLinkCurrent active";
            }
            $icon="";
            if($item->getIcon()!=""){
                $icon="<img src=\"images/".$item->getIcon()."\" alt=\"menuItemIcon\" class=\"menuItemIcon\"/>";
            }
                
            if($j>0){
            	echo " ".$seperator;            	
            }    
            echo "<span class=\"extendModulePages\"><a href=\"".$href."\" class=\"".$class."\">".$icon." ".$trans->getEntry($item->getLabel())."</a></span>"; 
            if($item->getSize()>0){
            	echo " ".$seperator;     
            	$this->printItemsFlat($item,$module,$request,$seperator);
            }                           	
        }        
	}
	
	/**
	 * Create a list-navigation where every entry is on one level
	 * @param array $request
	 * @param null|string $loginLink
	 * @param bool $addAdminLink
	 */
	public function printFlatListMenu($request, $loginLink=null, $addAdminLink=false, $collapseIfNotActive=false){
		$user=new XWUser();
		if(isset($_SESSION["XWUSER"])){
			$user=$_SESSION["XWUSER"];
		}
		
		echo "	<ul class=\"nav navbar-nav\">\n";
		
		$modules=XWModuleListFactory::getFullModuleList();
		for($i=0;$i<$modules->getSize();$i++){
			/**
			 * @var XWModule $module
			 */
			$module=$modules->getModule($i);
			if(!$module->isHidden()){
				if($module->hasUserPermission($user)){
					$class="";
					if($module->getCallName()==$request["page"] 
							&& (!isset($request["sub"]) || $request["sub"]=="index")){
						$class=" active";
					}
					
					$trans=new XWLocalePropertiesReader();
					//adds translation due dictionary-lib
					if(!XWAddonManager::instance()->getAddonByName("XWDictionaries")->existsIn($module->getCallName())){
						$trans->importPropertiesBundle($module->getDictionaryPath(),XWAddonManager::instance()->getAddonByName("XWLocale")->findLocale());
						XWAddonManager::instance()->getAddonByName("XWDictionaries")->addDictionary($module->getCallName(),$trans);
					}
					$trans=XWAddonManager::instance()->getAddonByName("XWDictionaries")->getDictionary($module->getCallName());
					
					?>
					<li class="module<?=$class ?>">
						<a href="index.php?page=<?=$module->getCallName() ?>&sub=index" title="<?=$trans->getEntry($module->getName()) ?>"><?=$trans->getEntry($module->getName()) ?></a>
					</li>
					<?php
					
					if(!$collapseIfNotActive || strlen($class)>0){
						$deployer=new XWModuleDeployer();
	         			$deployer->load($module);
	         			
	         			for($iS=0;$iS<$deployer->getSize();$iS++){
	         				$item=$deployer->getSubPageMenuItem($iS);
	         				if($item->isOnlyVisibleWithLogin()==false || $user->getId()>0){
	         					$subClass="";
	         					if(isset($request["sub"]) && $item->getLinkedPage()==$request["sub"]){
	         						$subClass=" active";	
	         					}	
	         					?>
	         					<li class="module-sub<?=$subClass ?>">
	         						<a href="index.php?page=<?=$module->getCallName() ?>&sub=<?=$item->getLinkedPage() ?>" title="<?=$trans->getEntry($item->getLabel()) ?>"><?=$trans->getEntry($item->getLabel()) ?></a>
	         					</li>
	         					<?php
	         				}	
	         			}
					}
				}
			}
		}
		
		$fullpages = $this->getFullPageList();
		for($i=0;$i<$fullpages->getSize();$i++){
			$page=$fullpages->getPage($i);
			if(!$page->isHidden()){
				$class="";
				if($module->getCallName()==$request["page"]
						&& !isset($request["sub"])){
					$class=" active";
				}
				
				echo $this->getPageLinkListItem($page, $class, false);
			}	
		}	
		
		if(intval($user->getId())==0 && strlen($loginLink)>0){
			?>
			<li class="login"><a href="<?=$loginLink ?>">Login</a></li>
			<?php
		}
		else{
			?>
			<li class="login"><a href="index.php?userLogout=<?=$user->getId() ?>">Logout</a></li>
			<?php
		}	
		if($addAdminLink && ($user->isInGroup("admins") || $user->isInGroup($this->adminGroup))){
			?>
			<li class="admin-panel"><a href="index.php?adminpage=1&page=index">Admin-Panel</a></li>
			<?php
		}	
		
		echo "	</ul>\n";
	}	
} 
