<?php
namespace xw\cli\pages;

use core\utils\config\GlobalConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WPImport extends Command {
    protected function configure(){
        $this->setName('aoop:pages:wpimport');
        $this->setDescription('import pages from an WordPress export (pages, only published)');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('file', null, InputOption::VALUE_REQUIRED, 'path of export file');
        $this->addOption('override', null, InputOption::VALUE_OPTIONAL, 'allow overriding existing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $file = $input->getOption('file');
        $instance = $input->getOption('instance');
        $template = $input->getOption('template');
        if($instance && is_file($file)){
            $override = false;
            if($input->hasOption('override')){
                $override = strlen($input->getOption('override')) > 0 ? $input->getOption('override') == 'true' : false;
            }

            $xml = new \DOMDocument();
            $xml->loadXML(file_get_contents($file));

            $folder = GlobalConfig::instance()->getValue('instancesfolder') . $instance . '/pages/';
            $items = $xml->getElementsByTagName('item');
            $cnt = 1;
            
            /** @var \DOMElement $item */
            foreach ($items as $item){
                $title = null;
                $name = null;
                $content = null;

                /** @var \DOMElement $child */
                foreach ($item->childNodes as $child){
                    if(strtolower($child->nodeName) == 'content:encoded'){
                        $content = $child->nodeValue;
                    }
                    else if(strtolower($child->nodeName) == 'wp:post_name'){
                        $name = $child->nodeValue;
                    }
                    else if(strtolower($child->nodeName) == 'title'){
                        $title = $child->nodeValue;
                    }
                }

                if($name && $content && $title){
                    if(strlen($name) > 0 && !is_file($folder . $name . '.html') || $override){
                        if(strlen($template) > 0 && is_file($folder . $template . '.html')){
                            $templateContent = file_get_contents($folder . $template . '.html');
                            $templateContent = preg_replace("/__page__/", "{% include '" . $name . "_content.html' %}", $templateContent);

                            file_put_contents($folder . $name . '.html', $templateContent);
                            file_put_contents($folder . $name . '_content.html', $content);
                            file_put_contents($folder . $name . '.xml', '<page><name>' . $title . '</name><hidden>true</hidden></page>');
                            file_put_contents($folder . $name . '_content.xml', '<page><name>' . $title . '</name><hidden>true</hidden></page>');
                            $output->writeln('#' . ($cnt++) . ' imported page ' . $title . ' (' . $name. '.html)');
                        }
                        else{
                            file_put_contents($folder . $name . '.html', $content);
                            file_put_contents($folder . $name . '.xml', '<page><name>' . $title . '</name><hidden>true</hidden></page>');
                            $output->writeln('#' . ($cnt++) . ' imported page ' . $title . ' (' . $name. '.html)');
                        }   
                    }
                    else{
                        $output->writeln('#' . ($cnt++) . ' ' . $name . ' existing, no overide allowed');
                    }
                }
            }
        }
        else{
            $output->writeln('file not found!');
        }
    }
}