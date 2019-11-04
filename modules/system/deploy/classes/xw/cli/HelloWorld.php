<?php
namespace xw\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorld extends Command{
    protected function configure(){
        $this->setName('aoop:helloworld');
        $this->setDescription('test command');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $output->writeln('HelloWorld!');
    }
}