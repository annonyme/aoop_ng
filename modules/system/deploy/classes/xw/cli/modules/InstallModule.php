<?php

namespace xw\cli\modules;

use core\modules\factories\XWModuleListFactory;
use core\modules\XWModule;
use core\utils\config\GlobalConfig;
use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallModule extends Command
{
    protected function configure()
    {
        $this->setName('aoop:modules:install');
        $this->setDescription('Install a module for an instance');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('module', null, InputOption::VALUE_REQUIRED, 'name of the module');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $instance = $input->getOption('instance');
        if(is_dir(GlobalConfig::instance()->getValue('instancesfolder') . $instance)){
            $moduleName = $input->getOption('module');
            $modules = XWModuleListFactory::getFullModuleList();
            /** @var XWModule $module */
            foreach ($modules->toArrayList() as $module){
                if($module->getCallName() == $moduleName && is_file($module->getPath() . 'deploy/install/install.sql')){
                    $sql = file_get_contents($module->getPath() . 'deploy/install/install.sql');
                    $dbName = XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
                    $db = PDBCCache::getInstance()->getDB($dbName);
                    $pdo = $db->getNativeConnection();

                    $pdo->exec($sql);
                }
            }
        }
        else{
            $output->writeln($instance . " is not a valid instance");
        }
    }
}

