<?php
namespace core\twig;

use core\addons\Services;
use core\addons\XWAddonImplementation;
use core\events\EventListenerFactory;
use core\net\XWUrlHelper;
use core\pages\plain\XWPageListFactory;
use core\utils\XWServerInstanceToolKit;
use Exception;
use Twig\Environment;
use Twig\TwigFunction;

class TwigFunctions{
    public static function decorateTwig(Environment $twig, $paths = []): Environment {
        $addonRendering = new TwigFunction('renderAddon',
            function($name){
                $result = '';
                try{
                    $vars = [];
                    if(preg_match("/\|\|/", $name)){
                        $parts = preg_split("/\|\|/", $name);
                        $name = $parts[0];
                        $vars = json_decode($parts[1], true);
                    }

                    $addon = Services::getContainer()->get($name);
                    if($addon && $addon instanceof XWAddonImplementation){
                        $result = $addon->render($vars);
                    }
                }
                catch(Exception $e){

                }
                return $result;
            }
        );

        $pagelink = new TwigFunction('pagelink',
            function($page){
                $result = '';
                $baseUrl = XWServerInstanceToolKit::instance()->getCurrentInstanceURL();
                $pages = XWPageListFactory::getFullPageList(Services::getContainer()->get('pageDir'));
                $pageObj = $pages->getPageByName($page);

                if($pageObj && $pageObj->getParent()){
                    $result = $baseUrl . $pageObj->getParent() . '/' . $pageObj->getCallName() . '.html';
                }
                else{
                    $result = $baseUrl . $page . '.html';
                }

                return $result;
            }
        );

        $modulelink = new TwigFunction('modulelink',
            function($page){
                $baseUrl = XWServerInstanceToolKit::instance()->getCurrentInstanceURL();
                $parts = preg_split("/\//", $page);
                if(count($parts) == 1 ||strlen($parts[1]) == 0){
                    $parts[1] = 'index';
                }
                return $baseUrl . $parts[0] . '/' . XWUrlHelper::simplyfyText($parts[1], isset($parts[2]) ? $parts[2] : '');
            }
        );

        $random = new TwigFunction('random',
            function($values){
                $valuesArray = preg_split("/[,;]/", $values);
                return $valuesArray[rand(0, count($valuesArray) -1)];
            }
        );

        $twig->addFunction($addonRendering);
        $twig->addFunction($pagelink);
        $twig->addFunction($modulelink);
        $twig->addFunction($random);

        if($paths && count($paths) > 0) {
            $twig->addTokenParser(new ExtTokenParser($paths));
            $twig->addTokenParser(new IncTokenParser($paths));
        }

        try{
            /** @var EventListenerFactory $events */
            $events = Services::getContainer()->get('events');
            $tmp = $events->fireFilterEvent('twig_functions_extend', $twig);
            if($tmp instanceof Environment){
                $twig = $tmp;
            }
        }
        catch(Exception $e){

        }

        return $twig;
    }
}