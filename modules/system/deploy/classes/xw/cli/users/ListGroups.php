<?php
namespace xw\cli\users;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use xw\entities\users\XWGroupList;
use Symfony\Component\Console\Input\InputOption;
use xw\cli\CLIInstanceInit;

class ListGroups extends Command{
    protected function configure(){
        $this->setName('aoop:users:groups:list');
        $this->setDescription('List all usergroups');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        CLIInstanceInit::init($input->getOption('instance'));
        $groups = new XWGroupList();
        $groups->load();
        $output->writeln("Groups:");
        $output->writeln("================");
        for($i=0;$i<$groups->getSize();$i++){
            $output->writeln(" - " . $groups->getGroup($i)->getName());
        }    
    }
}