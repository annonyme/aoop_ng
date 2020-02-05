<?php
namespace core\addons;

use core\twig\TwigFunctions;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class XWRenderingAddon{
    private $templatePath = '';
    private $overrideInstancePath = '';
    
    public function renderTemplate(string $templateName, array $model = []):string {
        $result = '';
        if(is_dir($this->templatePath)){            
            try{
                $paths = [];
                //TODO event + collection
                if(is_dir($this->overrideInstancePath)){
                    $paths['addonRef1'] = $this->overrideInstancePath;
                }
                $paths['base'] = $this->templatePath;

                $loader = new FilesystemLoader($paths);
                $twig = new Environment($loader);
                $twig = TwigFunctions::decorateTwig($twig, $paths);
                $result = $twig->render($templateName, $model);
            }
            catch(Exception $e){

            }
        }
        return $result;
    }

    public function getTemplatePath():string
    {
        return $this->templatePath;
    }

    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @return string
     */
    public function getOverrideInstancePath(): string
    {
        return $this->overrideInstancePath;
    }

    /**
     * @param string $overrideInstancePath
     */
    public function setOverrideInstancePath(string $overrideInstancePath)
    {
        $this->overrideInstancePath = $overrideInstancePath;
    }
}