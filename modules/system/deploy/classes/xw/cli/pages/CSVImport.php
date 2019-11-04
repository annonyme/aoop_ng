<?php
namespace xw\cli\pages;

use core\utils\config\GlobalConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CSVImport extends Command {
    protected function configure(){
        $this->setName('aoop:pages:csvimport');
        $this->setDescription('import pages from an CSV ([callname,title,content])');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('file', null, InputOption::VALUE_REQUIRED, 'path of csv file');
        $this->addOption('template', null, InputOption::VALUE_OPTIONAL, 'include page-content in a proxy-page');
        $this->addOption('delimiter', null, InputOption::VALUE_OPTIONAL, 'default is ,');
        $this->addOption('override', null, InputOption::VALUE_OPTIONAL, 'allow overriding existing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $file = $input->getOption('file');
        $instance = $input->getOption('instance');
        $template = $input->getOption('template');
        if($instance && is_file($file)){
            $delimiter = ',';
            $override = false;
            if($input->hasOption('delimiter')){
                $delimiter = strlen($input->getOption('delimiter')) > 0 ? $input->getOption('delimiter') : ',';
            }
            if($input->hasOption('override')){
                $override = strlen($input->getOption('override')) > 0 ? $input->getOption('override') == 'true' : false;
            }
            $fh = fopen($file, 'r');
            $folder = GlobalConfig::instance()->getValue('instancesfolder') . $instance . '/pages/';
            $cnt = 1;
            while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
                $line = fgetcsv($fh, 0, $delimiter);

                try{
                    if(strlen($line[0]) > 0 && !is_file($folder . $line[0] . '.html') || $override){
                        if(strlen($template) > 0 && is_file($folder . $template . '.html')){
                            $templateContent = file_get_contents($folder . $template . '.html');
                            $templateContent = preg_replace("/__page__/", "{% include '" . $line[0] . "_content.html' %}", $templateContent);

                            file_put_contents($folder . $line[0] . '.html', $templateContent);
                            file_put_contents($folder . $line[0] . '_content.html', $line[2]);
                            file_put_contents($folder . $line[0] . '.xml', '<page><name>' . $line[1] . '</name><hidden>true</hidden></page>');
                            file_put_contents($folder . $line[0] . '_content.xml', '<page><name>' . $line[1] . '</name><hidden>true</hidden></page>');
                            $output->writeln('#' . ($cnt++) . ' imported page ' . $line[1] . ' (' . $line[0]. '.html)');
                        }
                        else{
                            file_put_contents($folder . $line[0] . '.html', $line[2]);
                            file_put_contents($folder . $line[0] . '.xml', '<page><name>' . $line[1] . '</name><hidden>true</hidden></page>');
                            $output->writeln('#' . ($cnt++) . ' imported page ' . $line[1] . ' (' . $line[0]. '.html)');
                        }
                    }
                    else{
                        $output->writeln('#' . ($cnt++) . ' ' . $line[0] . ' existing, no overide allowed');
                    }
                }
                catch(\Exception $e){

                }
            }
        }
        else{
            $output->writeln('file "' . $file . '" not found!');
        }
    }
}