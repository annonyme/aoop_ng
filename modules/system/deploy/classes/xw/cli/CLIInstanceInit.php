<?php
namespace xw\cli;

use core\utils\XWServerInstanceToolKit;
use core\utils\config\GlobalConfig;

class CLIInstanceInit{
    public static function init($instanceName){
        if(is_dir(GlobalConfig::instance()->getValue('instancesfolder') . $instanceName)){
            XWServerInstanceToolKit::instance()->getServerSwitch('path:' . GlobalConfig::instance()->getValue('instancesfolder') . $instanceName);
        }
        else{
            throw new \Exception("invalid instance specified");
        }
    }
}