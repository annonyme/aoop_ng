<?php
namespace xw\cli\users;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use xw\entities\users\XWUser;
use xw\cli\CLIInstanceInit;

class ResetPassword extends Command{
    protected function configure(){
        $this->setName('aoop:users:reset-password');
        $this->setDescription('Set new password for a user');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'name of the user');
        $this->addOption('password', null, InputOption::VALUE_REQUIRED, 'the new password');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        CLIInstanceInit::init($input->getOption('instance'));
        $user = new XWUser();
        $user->loadByName($input->getOption('name'));
        if($user->getId() > 0){
            $password = trim($input->getOption('password'));
            if(strlen($password) > 0){
                $user->save($password);
                $output->writeln("new password was set for user " . $user->getName());
            }
            else{
                $output->writeln("password must consist of at last 1 none-whitespace char");
            }
        }
        else{
            $output->writeln("user is unkown");
        }
    }
}