<?php

use core\utils\config\GlobalConfig;
use core\modules\XWModuleClassLoader;
use PDBC\PDBCDBFactory;
use core\cli\CLIFactory;
use core\modules\factories\XWModuleListFactory;
use \core\addons\Services;

//include composer autoloader
$autoloader = require_once ("vendor/autoload.php");
$autoloader->addPsr4("core\\", "system/classes/core/");

//load global-cli-config
GlobalConfig::instance("globalconfig-cli.json");
$autoloader->addPsr4("PDBC\\", GlobalConfig::instance()->getValue("pdbcclassesfolder"));

//include modules classes
XWModuleClassLoader::addPsr4($autoloader);

//session_start();

//create db-connection  (PDBC V 2.0)
//---------------------------------------------------------------------------
$pdbcConfFolder=GlobalConfig::instance()->getValue("configspath")."pdbc/";
PDBCDBFactory::init($pdbcConfFolder);

//pre init system-services (from singletons to services)
Services::getContainer()->set('events', \core\events\EventListenerFactory::getInstance());
Services::getContainer()->set('systemLogger', \core\logging\XWLoggerFactory::getLogger(\core\utils\XWServerSwitch::class));
Services::getContainer()->set('mailer', \core\mail\SMTPMailerFactory::instance());
Services::getContainer()->set('autoloader', $autoloader);

try{
    $app = CLIFactory::createApp(XWModuleListFactory::getFullModuleList()->toArrayList());
    $app->run();
}
catch(Exception $e){
    var_dump($e);
}