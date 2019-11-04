<?php
namespace core\datastorage\ng;

use core\logging\XWLoggerFactory;

class FileHandlerFactory {
    public static function getHandlerByConfigFile($name, $file){
        return self::getHandler($name, json_decode(file_get_contents($file)));
    }

    public static function getHandler($name, $config){
        $result = null;
        try{
            if(isset($config['name'])){
                $refClass = new \ReflectionClass($config['class']);
                if($refClass){
                    $result = $refClass->newInstance($config['root'], $config['publicURI'], $config['user'], $config['credentials'], $config);
                }
            }
        }
        catch(\Exception $e){
            XWLoggerFactory::getLogger(self::class)->error($e);
        }
        return $result;
    }
}