<?php
use core\utils\filesystem\XWFileList;
use core\modules\factories\XWModuleListFactory;
use core\utils\XWServerInstanceToolKit;
use core\utils\config\GlobalConfig;

class XWThemeStylesLoader extends \core\addons\XWAddonImplementation {
	private $config = [];

    public function __construct(){
		try{
            $this->config = array_merge($this->config, ['env' => XWServerInstanceToolKit::instance()->getEnvValues()]);
        }
        catch(Exception $e){

        }
	}

	private function parseTwig($template){
        try{
            $loader = new \Twig_Loader_Array(['template' => $template]);
            $twig = new \Twig_Environment($loader);
            $twig = \core\twig\TwigFunctions::decorateTwig($twig);
            $template = $twig->render('template', $this->config);
        }
        catch(\Exception $e){

        }
        return $template;
    }
	
	private function copyAssets($theme, $sourcePath){
	    $path = "assets/".$theme;
	    if(is_dir($sourcePath."assets") && !is_dir($path)){
	        mkdir($path, 0775, true);
	        $di = new DirectoryIterator($sourcePath."assets");
	        foreach($di as $file){
	            if(!$file->isDot() && $file->isFile()){
	                copy($file->getPathname(), $path."/".$file->getFilename());
	                if(preg_match("/(\.css)|(\.js)$/i", $file->getFilename())){
                        try{
                            file_put_contents($path."/".$file->getFilename(), $this->parseTwig(file_get_contents($path."/".$file->getFilename())));
                        }
                        catch(Exception $e){

                        }
                    }
	            }
	        }
	    }
	}

    public function render($vars = []):string {
        return $this->printStyles(true);
    }
	
	public function printStyles($returnAsString = false){
		$toolKit=XWServerInstanceToolKit::instance();
		$switcher=$toolKit->getServerSwitch();
		$theme=$switcher->getTheme();	
		if(isset($_REQUEST["adminpage"]) && ($_REQUEST["adminpage"]=="1"||$_REQUEST["adminpage"]=="true") && $switcher->getAdminTheme()!=""){
			$theme=$switcher->getAdminTheme();
		}
		
		$modules = XWModuleListFactory::getFullModuleList("modules/");
		$modCheck = [];
		for($i=0; $i<$modules->getSize();$i++){
			$module=$modules->getModule($i);
			$modCheck["module_".$module->getCallName().".css"] = "";
			if(is_file($module->getPath()."/deploy/default.css")){
				$modCheck["module_".$module->getCallName().".css"] = $module->getPath()."/deploy/default.css";
			}
		}			
			
		$root = GlobalConfig::instance()->getValue("themespath") . $theme . "/";
        $result = \hannespries\events\EventHandler::getInstance()->fireFilterEvent('theme_load_by_name', ['path' => null], ['theme' => $theme, 'admin' => false]);
        if(isset($result['path']) && file_exists($result['path'])){
            $root = $result['path'];
        }

		$this->copyAssets($theme, $root);
		$styleFiles=[];
		if(is_dir($root)){
			$styles=new XWFileList();
			$styles->load($root);
			for($i=0;$i<$styles->getSize();$i++){
				if(preg_match("/\.css$/i", $styles->getFile($i))){					
					if(preg_match("/^module_/i", $styles->getFile($i)) && isset($modCheck[$styles->getFile($i)])){
						$styleFiles[]=$root."".$styles->getFile($i);
						$modCheck[$styles->getFile($i)]="";
					}
					else{
						$styleFiles[]=$root."".$styles->getFile($i);
					}					
				}
			}
			foreach($modCheck as $modStyle){
				if($modStyle!=""){
					$styleFiles[]=$modStyle;
				}
			}
		}

        $result = '';
		if(count($styleFiles) > 0){
		    $result .= "<style type=\"text/css\">\n";
			foreach ($styleFiles as $style){
                $result .=  $this->parseTwig(file_get_contents($style));
			}
            $result .=  "</style>";
		}

		if(!$returnAsString){
		    echo $result;
        }
        return $result;
	}
}
