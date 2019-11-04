<?php
namespace core\cli;

use core\events\EventListenerFactory;
use core\utils\config\GlobalConfig;
use hannespries\events\EventHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
/**
 * ['calss1','class2'] in module/deploy/cli.json
 * 
 * @author annonyme
 *
 */
class CLIFactory{
    /**
     * @param \Iterator|null $modules
     * @return Application
     * @throws \Exception
     */
    public static function createApp(\Iterator $modules = null): Application{
        $app = new Application();
        try{
            /** @var \core\modules\XWModule $module */
            foreach ($modules as $module){
                if(is_file(GlobalConfig::instance()->getValue("modulesfolder") . $module->getCallName() . "/deploy/cli.json")){
                    $json = json_decode(file_get_contents(GlobalConfig::instance()->getValue("modulesfolder") . $module->getCallName() . "/deploy/cli.json") ,true);
                    if(is_array($json)){
                        foreach($json as $command){
                            try{
                                $clazz = new \ReflectionClass($command);
                                $obj = $clazz->newInstance();
                                if($obj instanceof Command){
                                    $app->add($obj);
                                }
                            }
                            catch(\Exception $eCommand){
                                var_dump($eCommand);
                            }
                        }
                    }
                }
            }

            $commands = EventHandler::getInstance()->fireFilterEvent('collecting_cli_commands', []);
            foreach ($commands as $command){
                if($command instanceof Command){
                    $app->add($command);
                }
            }
        }
        catch(\Exception $e){
            throw new \Exception("error on read modules cli-command", 0, $e);
        }
        return $app;
    }
}