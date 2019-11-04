<?php
namespace xw\cli;

use core\logging\XWLoggerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogTest extends Command{
    protected function configure(){
        $this->setName('aoop:logtest');
        $this->setDescription('testing logfile error');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $logger = XWLoggerFactory::getLogger(self::class);
        $logger->log('e', 'test');
        $output->writeln('log: test.');
    }
}