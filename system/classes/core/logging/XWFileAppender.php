<?php
namespace core\logging;

class XWFileAppender implements XWAppender {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see XWAppender::write()
	 */
	public function write($msg, ?\Exception $e, $type, array $appenderConfig) {
		$dateFormat="Y-m-d h:i:s";
        if(isset($appenderConfig["dateformat"])){
           	$dateFormat=$appenderConfig["dateformat"];
        }
        $ip="";
        if(isset($appenderConfig["remoteip"]) && ($appenderConfig["remoteip"]===true || $appenderConfig["remoteip"]=="true")){
          	if(isset($_SERVER["REMOTE_ADDR"])){
                $ip=" [".$_SERVER["REMOTE_ADDR"]."]";
            }
            else{
                $ip=" [no-remote-ip]";
            }
        }
        $content = strtoupper($type).$ip." [".date($dateFormat)."]: ".$msg;
        if($e!=null && $appenderConfig["exceptionFullStackTrace"]){
             $content.="\n".$e->getTraceAsString();
        }
        if(!file_exists($appenderConfig["filename"])){
           	if(!file_exists(preg_replace("/[a-zA-Z0-9_.]+$/Uis", "", $appenderConfig["filename"]))){
           		mkdir(preg_replace("/[a-zA-Z0-9_.]+$/Uis", "", $appenderConfig["filename"]));
           	}
        }
        file_put_contents($appenderConfig["filename"], $content."\n", FILE_APPEND);
	}
}