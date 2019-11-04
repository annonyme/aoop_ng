<?php
namespace core\addons;

class XWRenderingAddon{
    private $templatePath = '';
    private $overrideInstancePath = '';
    
    public function renderTemplate(string $templateName, array $model = []):string {
        $result = '';
        if(is_dir($this->templatePath)){            
            try{
                $pathes = [$this->templatePath];
                if(is_dir($this->overrideInstancePath)){
                    $pathes[] = $this->overrideInstancePath;
                }

                $loader = new \Twig_Loader_Filesystem($pathes);
                $twig = new \Twig_Environment($loader);
                $twig = \core\twig\TwigFunctions::decorateTwig($twig);
                $result = $twig->render($templateName, $model);
            }
            catch(\Exception $e){

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