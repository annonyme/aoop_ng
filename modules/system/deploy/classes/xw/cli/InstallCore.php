<?php

namespace xw\cli;

use core\utils\config\GlobalConfig;
use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCore extends Command
{
    protected function configure()
    {
        $this->setName('aoop:core:install');
        $this->setDescription('Install the core-system (edit userdata/config/pdbc/datasources.xml before!)');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        if(is_file(GlobalConfig::instance()->getValue('modulesfolder') . '../etc/install/seperate/aoop_core.sql')){
            $sql = file_get_contents(GlobalConfig::instance()->getValue('modulesfolder') . '../etc/install/seperate/aoop_core.sql');
            $dbName = XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
            $db = PDBCCache::getInstance()->getDB($dbName);
            $pdo = $db->getNativeConnection();
            $pdo->exec($sql);
        }
    }
}