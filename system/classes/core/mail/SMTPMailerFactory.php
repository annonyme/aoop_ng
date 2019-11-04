<?php
namespace core\mail;

use core\utils\XWServerInstanceToolKit;

class SMTPMailerFactory{
    private static $instance = null;
    private $config=[];
    
    public static function instance(): SMTPMailerFactory{
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct(){
        $folder = XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath();
        if(is_file($folder."/mail-config.json")){
            $this->config=json_decode(file_get_contents($folder."/mail-config.json"), true);
        }
    }
    
    public function createMailer(){
        $mailer = new SMTPMailer('',0,'','');
        if(isset($this->config["host"])){
            $mailer = new SMTPMailer(
                $this->config["host"],
                (int) $this->config["port"],
                $this->config["user"],
                $this->config["password"]);
        }        
        return $mailer;
    }
}