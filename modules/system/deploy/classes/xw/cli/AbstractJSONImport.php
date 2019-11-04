<?php
namespace xw\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractJSONImport extends Command{
    
    protected $commandName = '';
    protected $archiveName = 'archive';
    
    protected function configure(){
        $this->setName($this->commandName);
        $this->setDescription('import new clubs');
        $this->addOption('instance', null, InputOption::VALUE_REQUIRED, 'name of the instance');
        $this->addOption('folder', null, InputOption::VALUE_REQUIRED, 'path to the folder');
    }
    
    abstract protected function importData(array $data = []):bool;
    
    /**
     * 
     * @param array|\DirectoryIterator[] $files
     */
    private function copyToArchive(array $files = []){        
        foreach($files as $file){
            $seperator = DIRECTORY_SEPARATOR;
            try{
                $folder = $file->getPath() . $seperator . $this->archiveName . $seperator;
                if(!is_dir($folder)){
                    mkdir($folder, 0777, true);
                }
                copy($file->getRealPath(), $folder . $file->getFilename());
                unlink($file->getRealPath());
            }
            catch(\Exception $e){
                
            }
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        CLIInstanceInit::init($input->getOption('instance'));
        if(is_dir($input->getOption('folder'))){
            $di = new \DirectoryIterator($input->getOption('folder'));
            $copyToArchive = [];
            foreach ($di as $file){
                if($file->isFile() && preg_match("/\.json$/i", $file->getFilename())){
                    $json = json_decode(file_get_contents($file->getRealPath()), true);
                    if(is_array($json) && $this->importData($json)){
                        $copyToArchive[] = $file;
                    }
                }
            }
            $this->copyToArchive($copyToArchive);
        }
    }    
}