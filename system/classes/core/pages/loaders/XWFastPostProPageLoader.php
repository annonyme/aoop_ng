<?php
namespace core\pages\loaders;

use core\addons\Services;
use core\events\EventListenerFactory;
use core\modules\controllers\XWModulePageRenderingResult;
use core\modules\factories\XWModuleListFactory;
use core\pages\plain\XWPageListFactory;
use core\twig\TwigFunctions;
use core\utils\XWLocalePropertiesReader;
use core\utils\XWServerInstanceToolKit;
use Exception;
use ReflectionClass;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use xw\entities\users\XWUser;
use core\addons\XWAddonManager;
use core\logging\XWLogger;
use core\logging\XWLoggerFactory;
use core\net\XWRequest;
use core\security\XWFormSecurity;
use XWDictionaries;
use XWLocale;

/*
* Copyright (c) 2013/2015/2016/2017/2018/2020 Hannes Pries <https://www.hannespries.de>
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

class XWFastPostProPageLoader implements XWPageLoaderInterface{

    private $pageDir = '';
    private $moduleDir = '';

    private $titleAdd = '';
    private $directOutput = false;
    private $logger = null;
	
	public function __construct(){
		$this->logger=XWLoggerFactory::getLogger(self::class);
	}
	
	public function	setPageDir($pageDir){
		$this->pageDir=$pageDir;
	}
	
	public function setModuleDir($moduleDir){
		$this->moduleDir=$moduleDir;
	}

	private function getTwigEnvModel(){
        $model=['request' => XWRequest::instance()->getRequestAsArray(), 'test' => 'TEST!'];
        if(isset($_SESSION['XWUSER'])){
            $model['user'] = $_SESSION['XWUSER'];
        }

        //to avoid CSRF-attacks https://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
        $model['formsecurity'] = [
            'name' => XWFormSecurity::getRequestParameterName(),
            'value' => XWFormSecurity::getURLParameterWithSessionSecTokenValueOnly(),
        ];

        $model['env'] = XWServerInstanceToolKit::instance()->getEnvValues();

	    return $model;
    }
	
	/**
	 * $sub is optional (but should be set if avaible), $noContainerDiv is optional for normal text pages, but maybe have to be
	 * set for binary- or xml-output (Image-Creation or Ajax-Backend). $static is optinal, but could be later
	 * used for caching purpose (in a later version).
	 */
	private function parseAndPrintOutput($outputString, $page, $sub='', $noContainerDiv=true, $parserName=''){
        if($parserName == 'twig'){
            try{
                $loader = new ArrayLoader(['template' => $outputString]);
                $twig = new Environment($loader);
                $twig = TwigFunctions::decorateTwig($twig);
                $outputString = $twig->render('template', $this->getTwigEnvModel());
            }
            catch(Exception $e){

            }
        }

	    //noContainerDiv for download pages
		if(!$noContainerDiv){
			$outputString="\n<div id=\"pagecontent_".$page."_".$sub."\" class=\"pagestyle_".$page."_".$sub."\">\n".$outputString."\n</div>\n";			
		}

		return $outputString;
	}
	
	/**
	 * @param string $text
	 */
	public function setTitleAdd($text){
		$this->titleAdd = $text;
	}
	
	public function load($pageName,$request = []){
		$this->loadPage($pageName, null ,$request, isset($request['adminpage']) ? $request['adminpage'] == 1 : false);
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		/** @var XWLocale $locale */
		$locale = Services::getContainer()->get('XWLocale');
		return $locale->findLocale();
	}
	
	public function loadPage($pageName, $sub=null, $request=[], $adminPage=false){
        $res=new XWPageLoaderResult();

	    $pageName = preg_replace("/[<>=%\s().]/", '', $pageName);
		$adminGroup=XWServerInstanceToolKit::instance()->getServerSwitch()->getAdmins();
		$addonManager=XWAddonManager::instance(); //TODO replace with Services::getContainer()
		$dict=new XWLocalePropertiesReader();
		
		$user=new XWUser();
		if(isset($_SESSION['XWUSER']) && $_SESSION['XWUSER']->getId() > 0){
			$user=$_SESSION['XWUSER'];
		}

		$pagesList = XWPageListFactory::getFullPageList($this->pageDir);
		$modules=XWModuleListFactory::getFullModuleList();
		if($adminPage){
			$res->setTitleAdd('Admin-Panel');
		    if($pageName===null || trim($pageName) == '' || $pageName == 'index'){
				$request['page']='system';	
				$pageName = 'system';
			}
			if($sub===null || trim($sub) == ''){
				$request['sub']='index';
				$sub='index';
			}
			if(isset($_SESSION['XWUSER']) && ($_SESSION['XWUSER']->isInGroup($adminGroup) || $_SESSION['XWUSER']->isInGroup('admins'))){
				if($modules->exists($pageName)){
													 
					$adminMod=$modules->getModuleByCallName($pageName);
								 
					if($adminMod!=null && file_exists($adminMod->getPath().'/deploy/admin/'.$sub.'.php')){
						if($adminMod->getAdminGroup() == '' || $user->isInGroup("admins") || $user->isInGroup($adminMod->getAdminGroup())){
							/** @var XWDictionaries $dictionaries */
							$dictionaries = Services::getContainer()->get('XWDictionaries');
                            if($adminMod->getDictionaryPath()!="" && $dictionaries!=null){
                                if(!$dictionaries->existsIn($adminMod->getCallName())){
                                    $dict->importPropertiesBundle($adminMod->getDictionaryPath(), $this->getLocale());
                                    $dictionaries->addDictionary($adminMod->getCallName(), $dict);
                                }
                                else{
                                    //used by include
                                    $dict = $dictionaries->getDictionary($adminMod->getCallName());
                                }
                            }

						    ob_clean();
                            ob_start();

						    include($adminMod->getPath().'/deploy/admin/'.$sub.'.php');

                            $outputString=ob_get_contents();
                            $res->setPageContent($this->parseAndPrintOutput($outputString, 'adminpage', $pageName));
                            ob_end_clean();
						}						
					}
					//TODO implements MVC for admin-panel
                }
            }
            else{
            	$this->logger->log(XWLogger::WARNING, "no admin-rights or not logged in [adminpage:".$pageName."]");
                $res->setPageContent("<span id=\"moduleAccessPermissionError\" class=\"moduleAccessPermissionErrorStyle\">no admin-rights or not logged in!</span>");
            }
		}
		else if(strlen($pagesList->getPageByName($pageName)->getCallName()) > 0){
			//old aoop .html + xml sidecar-file styles pages
			$locale = '';
			if($addonManager->getAddonByName('XWLocale')){
				$locale = $this->getLocale();
			}
		
			$page = $pagesList->getPageByName($pageName);
			 
			if($page->getDictionaryPath() != '' && $locale!=null && $locale != ''){
				$dict->importPropertiesBundle($page->getDictionaryPath(),$locale);
				/** @var XWDictionaries $dictionaries */
				$dictionaries = Services::getContainer()->get('XWDictionaries');
                $dictionaries->addDictionary($page->getCallName(), $dict);
			}
		
			if(!$page->isBackup() && $page->checkRestriction($user)){
				//simple page (standard)... simple include
				if($page->getPath()!=""){
				    $paths = [XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'pages'];
                    $loader = new FilesystemLoader($paths);
                    $twig = new Environment($loader);
                    $twig = TwigFunctions::decorateTwig($twig);

                    $model = $page->getValues();
                    $model['env'] = XWServerInstanceToolKit::instance()->getEnvValues();
                    $model['request'] = XWRequest::instance()->getRequestAsArray();
					if(isset($_SESSION['XWUSER'])){
                        $model['user'] = $_SESSION['XWUSER'];
                    }
					else {
						$model['notLoggedIn'] = true;
					}

                    $outputString = '';
                    try{
                        $outputString = $twig->render($page->getCallName() . '.html', $model);
                    }
                    catch(Exception $e){

                    }

					$res->setTitleAdd($page->getName());
					$res->setMetaDescription($page->getMetaDescription());
					
					$res->setPageContent($this->parseAndPrintOutput($outputString,$page->getCallName(),false, true));

                    $events = EventListenerFactory::getInstance();
                    $res = $events->fireFilterEvent('Dispatch_Page', $res, ['page' => $page, 'model' => $model, 'twig' => $twig]);
                	$res = $events->fireFilterEvent('Dispatch_Page_' . $page->getCallName(), $res, ['page' => $page, 'model' => $model, 'twig' => $twig]);
				}
			}
		}
		else if($modules->exists($pageName)){
			$module=$modules->getModuleByCallName($pageName);
                             
            if($module!=null && $module->getCallName()!='' && $module->hasUserPermission($user)){
                //load dictionaries
            	if($module->getDictionaryPath()!='' && $addonManager->getAddonByName('XWLocale')!=null){                       
					/** @var XWDictionaries $dictionaries */
					$dictionaries = Services::getContainer()->get('XWDictionaries');   
					if(!$dictionaries->existsIn($module->getCallName())){
						$dict->importPropertiesBundle($module->getDictionaryPath(), $this->getLocale());
						$dictionaries->addDictionary($module->getCallName(), $dict);
					}
					else{
						$dictionaries->getDictionary($module->getCallName());
					}
            	}
                             	
            	if($sub==null || trim($sub)==""){
            		$sub="index";
            		$request["sub"]=$sub;
            	}
            	
                if(file_exists($module->getPath()."/".$sub.".php")){        
                	$nonText=false;

                    if($module->existsInNonTextPages($sub)){
                    	$nonText=true;
                    	//TODO remove.. use rest.xml to define rest-services
                    	$this->logger->log(XWLogger::NOTICE, "setting deprecated CORS-header: ".$module->getPath()."/".$sub);
                    	if($module->existsInJsonPages($sub)){
                    		header('Access-Control-Allow-Origin: *');
							header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
							header('Access-Control-Max-Age: 1000');
							header('Access-Control-Allow-Headers: Content-Type');
                    	}                    	
                    }
                    ob_clean();
                    ob_start();
                    include($module->getPath() . '/' . $sub . '.php');
                    $outputString = ob_get_contents();
                    ob_end_clean();
                        
                    $res->setPageContent($this->parseAndPrintOutput($outputString,$module->getCallName(),$sub,$nonText));
                }
                else if(is_file($module->getPath()."/".$sub.".twig.json")){
                	try{
                		$pageData = json_decode(file_get_contents($module->getPath()."/".$sub.".twig.json"), true);
                		$controllerClazz = new ReflectionClass($pageData['controller']);
                		$controller = $controllerClazz->newInstance();
                		$controller->setDictionary($dict);
                        $controller->setModule($module);

                        //TODO to manipulate controller results or controller data
//                		$events = EventListenerFactory::getInstance();
//                		$modelResult = $events->perform('Dispatch_Controller_' . preg_replace("/\\/", '_', get_class($controller)) , 'result', $controller, [], false, true);
                		/** @var XWModulePageRenderingResult $modelResult */
                        $modelResult = $controller->result();

                		$model=['request' => XWRequest::instance()->getRequestAsArray(), 'model' => $modelResult->getModel(), 'dict' => $dict->getAsArray()];
                		if(isset($_SESSION["XWUSER"])){
                			$model['user'] = $_SESSION["XWUSER"];
                		}
                		
                		//to avoid CSRF-attacks https://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
                		$model["formsecurity"] = [
                		    "name" => XWFormSecurity::getRequestParameterName(),
                		    "value" => XWFormSecurity::getURLParameterWithSessionSecTokenValueOnly(),
                		];

                        $model['env'] = XWServerInstanceToolKit::instance()->getEnvValues();
                        $model['moduleAlias'] = $module->getCallName();
                        if(isset(XWServerInstanceToolKit::instance()->getEnvValues()['urlalias_' . $module->getCallName()])){
                            $model['moduleAlias'] = XWServerInstanceToolKit::instance()->getEnvValues()['urlalias_' . $module->getCallName()];
                        }
                		
                		//$model["messages"] = DisplayMessageFactory::instance()->getAllAndClear();
                		
                		//inner module redirect
                		if($modelResult->getAlternativeTemplate()){
                			$pageData['template'] = $modelResult->getAlternativeTemplate();
                		}
                		
                		$paths = [];
                        $paths[] = $module->getPath()."/templates/";
                        $paths[] = XWServerInstanceToolKit::instance()->getCurrentThemePath()."templates/".$module->getName()."/";
                        $paths[] = XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath()."templates/".$module->getName()."/";


                        /** @var EventListenerFactory $events */
                        $events = Services::getContainer()->get('events');
                        $paths = $events->fireFilterEvent('Twig_Views_Collection_paths', $paths, ['subject' => $module, 'controller' => $controller]);

                        $pathCache = [];
                        for($i = count($paths) -1; $i >= 0; $i--) {
                            if($i == 0) {
                                $paths['base'] = $paths[$i];
                            }
                            else {
                                $paths['pathRef' . $i] = $paths[$i];
                            }
                        }

                		$loader = new FilesystemLoader($paths);
                		$twig = new Environment($loader);
                        $twig = TwigFunctions::decorateTwig($twig, $pathCache);
                		
                		$outputString = $twig->render($pageData['template'], $model);
                		$res->setTitleAdd(strlen($modelResult->getTitle()) > 0 ? $modelResult->getTitle() : $module->getName());
						$res->setNoRendering($modelResult->isNoRendering());
						$res->setContentType($modelResult->getContentType());
                		$res->setPageContent($this->parseAndPrintOutput($outputString,$module->getCallName(), $sub, $module->existsInNonTextPages($sub)));
                	}
                	catch(Exception $e){
                		$this->logger->log(XWLogger::WARNING,$e->getMessage(), $e);
                	}                	
                }
                else{
                    $res->setPageContent("<span id=\"pageNotFoundError\" class=\"pageNotFoundErrorStyle\">Page not found! ([modules]/".$module->getCallName()."/".$sub.")</span>");
                    $this->logger->log(XWLogger::WARNING, "Page not found! ([modules]/".$module->getCallName()."/".$sub.")");
                    if($this->directOutput){
                        $res->setNoRendering(true);
                    }
                }                            	
            }
            else{
                $res->setPageContent("<span id=\"moduleAccessPermissionError\" class=\"moduleAccessPermissionErrorStyle\">no permission! [".$module->getName()."]</span>");
                $this->logger->log(XWLogger::WARNING, "no permission! ([modules]/".$module->getCallName()."/".$sub.")");
                if($this->directOutput){
                	$res->setNoRendering(true);
                }
            }
		}		
		else{
            $res->setPageContent("<span id=\"pageNotFoundError\" class=\"pageNotFoundErrorStyle\">Page not found (".$pageName.")!</span>");
			$this->logger->log(XWLogger::WARNING, "Page not found! (".$pageName.")");
			if($this->directOutput){
                $res->setNoRendering(true);
			}
		}

		return $res;
	}
}
