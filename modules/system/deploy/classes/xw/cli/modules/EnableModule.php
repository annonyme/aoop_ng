<?php
namespace xw\cli\modules;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use core\utils\config\GlobalConfig;

class EnableModule extends Command{
    protected function configure(){
        $this->setName('aoop:modules:enable');
        $this->setDescription('Enable a module for an instance');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('module', null, InputOption::VALUE_REQUIRED, 'name of the module');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        $instance = $input->getOption('instance');
        if(is_dir(GlobalConfig::instance()->getValue('instancesfolder') . $instance)){
            $module = $input->getOption('module');
            if(is_dir(GlobalConfig::instance()->getValue('modulesfolder') . $module)){
                if(!is_file(GlobalConfig::instance()->getValue('instancesfolder') . $instance . '/modules/' . $module . '.xml')){
                    $data = '<module></module>';
                    file_put_contents(GlobalConfig::instance()->getValue('instancesfolder') . $instance . '/modules/' . $module . '.xml', $data);
                }
                $output->writeln($module . ' is active in ' . $instance);
            }
            else{
                $output->writeln($module . " is not a valid module");
            }
        }
        else{
            $output->writeln($instance . " is not a valid instance");
        }
    }
}