<?php
namespace xw\cli\users;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use xw\entities\users\XWGroupList;
use xw\entities\users\XWUser;
use xw\cli\CLIInstanceInit;

class RemoveFromGroup extends Command{
    protected function configure(){
        $this->setName('aoop:users:groups:remove');
        $this->setDescription('Remove user from usergroup');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'name of the user');
        $this->addOption('group', null, InputOption::VALUE_REQUIRED, 'name of the group');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        CLIInstanceInit::init($input->getOption('instance'));
        $user = new XWUser();
        $user->loadByName($input->getOption('name'));
        if($user->getId() > 0){
            $groups = new XWGroupList();
            $groups->load();
            $found = false;
            for($i=0;$i<$groups->getSize();$i++){
                if($groups->getGroup($i)->getName() == $input->getOption('group')){
                    $groups->getGroup($i)->removeUserFrom($user);
                    $output->writeln("user was removed from group");
                }
            }
            
            if(!$found){
                $output->writeln("the group doesn't exists");
            }
        }
        else{
            $output->writeln("user is unkown");
        }
    }
}