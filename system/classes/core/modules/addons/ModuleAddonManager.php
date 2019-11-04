<?php
namespace core\modules\addons;

use core\utils\XWServerInstanceToolKit;
use ReflectionClass;
use DOMDocument;
use core\modules\factories\XWModuleListFactory;
use core\utils\config\GlobalConfig;
use core\addons\XWRenderingAddon;

class ModuleAddonManager{
	/**
	 * @var ModuleAddonManager
	 */
	private static $instance = null;
	
	/**
	 * @var ModuleAddon
	 */
	private $addons = [];
	
	private $createdAddons = [];

	public static function instance(){
		if(self::$instance === null){
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	private function init(){
		if(class_exists("XWModuleListFactory") && class_exists("XWServerInstanceToolKit")){
			$itk = XWServerInstanceToolKit::instance();
			$instancePath = $itk->getCurrentInstanceDeploymentRootPath();
			$modules = XWModuleListFactory::getFullModuleList();
			for($i = 0; $i < $modules->getSize(); $i++){
				$mod = $modules->getModule($i);
				$deployPath = $mod->getPath() . "/".GlobalConfig::instance()->getValue("moduledeployfolder");
				if(is_file($deployPath . "addons.json")){
					$adds = json_decode(file_get_contents($deployPath . "addons.json"),true);
					if(isset($adds["addons"])){
						foreach($adds["addons"] as $add){
							$addon = new ModuleAddon();
							$addon->setClazz($add["clazz"]);
							$addon->setModuleCallname($mod->getCallName());
							if(isset($add["name"])){
								$addon->setName($add["name"]);
							}
							else{
								$parts = preg_split("/[^a-zA-Z0-9]/", $addon->getClazz());
								$addon->setName($parts[count($parts) -1]);
							}
								
							if(isset($add["autostartup"])){
								$addon->setAutoStartup($add["autostartup"] === true || $add["autostartup"] === "true");
							}
								
							if(is_file($instancePath . "/" . $addon->getName()."-addonconfig.xml")){
								$addon->setConfigFilePath($instancePath . "/" . $addon->getName()."-addonconfig.xml");
							}
							else if(is_file($deployPath . "addons/" .$addon->getName().".xml")){
								$addon->setConfigFilePath($deployPath . "addons/" .$addon->getName().".xml");
							}
							
							if(is_dir($deployPath . "addons/" .$addon->getName()."/templates/")){
							    $addon->setTemplatePath($deployPath . "addons/" .$addon->getName()."/templates/");
							}
								
							$this->addAddon($addon);
						}
					}
				}
			}
		}
	}

	public function existsAddon($name):bool {
		return isset($this->addons[$name]);
	}

	public function addAddon(ModuleAddon $addon){
		if(!isset($this->addons[$addon->getName()])){
			$this->addons[$addon->getName()] = $addon;
		}
		$this->addons[$addon->getModuleCallname() . ":" . $addon->getName()] = $addon;
	}

	public function getAddon(string $name){
		$result = null;
		if($this->existsAddon($name)){
			if(!isset($this->createdAddons[$name])){
				$this->createdAddons[$name] = $this->createAddonInstance($this->addons[$name]);
			}
			$result = $this->createdAddons[$name];
		}
		return $result;
	}

	private function createAddonInstance(ModuleAddon $addon){
		$result = null;
		try{
			$values = $this->readInConfigParamsFromFile($addon->getConfigFilePath());
			$ref = new ReflectionClass($addon->getClazz());
			$result = $ref->newInstance();
			
			//set template path if exists and it is a template rendering addon
			if($result instanceof  XWRenderingAddon && is_dir($addon->getTemplatePath())){
			    $result->setTemplatePath($addon->getTemplatePath());
			}
			
			foreach($values as $key => $value){
				if($ref->hasProperty($key)){
					$prop = $ref->getProperty($key);
					$prop->setAccessible(true);
					$prop->setValue($value, $result);
				}
			};
		}
		catch(\Exception $e){

		}
		return $result;
	}

	private function readInConfigParamsFromFile(string $filename):array {
		$params = [];
		if(is_file($filename)){
			$doc = new DOMDocument();
			$doc->load($filename);
			$config = $doc->getElementsByTagName("config");
			if($config->item(0)->hasChildNodes()){
				$nodes = $config->item(0)->childNodes;
				$node = null;
				foreach ($nodes as $node){
					$key = $node->nodeName;
					$value = $node->nodeValue;
					if($key != "#text"){
						$params[$key] = $value;
					}
				}
			}
		}
		return $params;
	}

	public function callAllAutoStartup(){
		foreach($this->addons as $addon){
			/** @var ModuleAddon $addon */
		    if($addon->isAutoStartup()){
				$obj = $this->getAddon($addon->getName());
				try{
					$ref = new ReflectionClass(get_class($obj));
					if($ref->hasMethod("startup")){
						$ref->getMethod("startup")->invoke($obj);
					}
				}
				catch(\Exception $e){

				}
			}
		}
	}
}