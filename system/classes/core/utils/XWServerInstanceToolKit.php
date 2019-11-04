<?php
/*
 * Created on 09.01.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2009/2013/2014 Hannes Pries <http://www.annonyme.de>
  * Permission is hereby granted, free of charge, to any person obtaining a 
  * copy of this software and associated documentation files (the "Software"), 
  * to deal in the Software without restriction, including without limitation 
  * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
  * and/or sell copies of the Software, and to permit persons to whom the 
  * Software is furnished to do so, subject to the following conditions:
  * 
  * The above copyright notice and this permission notice shall be included in 
  * all copies or substantial portions of the Software.
  * 
  * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
  * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
  * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
  * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
  * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
  * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
  * IN THE SOFTWARE.
  */  

namespace core\utils;
 
use core\utils\config\GlobalConfig;

class XWServerInstanceToolKit{
	private $instanceName="";
	private $url="";
	
	private $serverSwitch="";

	static private $instance=null;
	
	static public function instance(){
		if(self::$instance==null){
			self::$instance=new XWServerInstanceToolKit();
		}
		return self::$instance;
	}

	
	public function __construct(){
		$this->url=$this->getCurrentInstanceURLWithParametersIntern();
		$this->serverSwitch=new XWServerSwitch($this->url);
		$this->instanceName=$this->serverSwitch->getName();
	}
	
	public function getCurrentInstanceName(){		
		return $this->instanceName;
	}

    /**
     * @param string $instanceName
     */
    public function setInstanceName($instanceName = 'default')
    {
        $this->instanceName = $instanceName;
    }
	
	public function getCurrentInstanceDeploymentDescriptorPath(){
		return GlobalConfig::instance()->getValue("instancesfolder").$this->getCurrentInstanceName()."/deploy.xml";
	}
	
	public function getCurrentInstanceDeploymentRootPath(){
		return GlobalConfig::instance()->getValue("instancesfolder").$this->getCurrentInstanceName()."/";
	}
	
	public function getCurrentThemePath(){
		return GlobalConfig::instance()->getValue("themespath").$this->serverSwitch->getTheme()."/";
	}
	
	public function getCurrentInstanceURL(){
		$urlparts=preg_split("/\//",$_SERVER["PHP_SELF"]);
        $curUrl="";
        $partsCount=count($urlparts);
        for($i=0;$i<$partsCount-1;$i++){
            $curUrl.=$urlparts[$i]."/";
        }
        $thisPageURL="http://".$_SERVER["HTTP_HOST"].$curUrl;
        return $thisPageURL;
	}
	
	private function getCurrentInstanceURLWithParametersIntern(){
		$result = null;
		if(isset($_SERVER["HTTP_HOST"])){
            $urlparts=preg_split("/\//",$_SERVER["PHP_SELF"]);
            $curUrl="";
            $partsCount=count($urlparts);
            for($i=0;$i<$partsCount-1;$i++){
                $curUrl.=$urlparts[$i]."/";
            }
            $thisPageURL="http://".$_SERVER["HTTP_HOST"].$curUrl;
            $vars="?";

            reset($_REQUEST);
            while (list($key, $value) = each($_REQUEST)) {
                if(preg_match("/(^page$)|(^sub$)|(^print$)|(^adminpage$)/i",$key)){
                    if(strlen($vars)==1){
                        $vars .= "".$key."=".$value."";
                    }
                    else{
                        $vars .= "&".$key."=".$value."";
                    }
                }
            }
            $result = $thisPageURL.$vars;
        }
	    return $result;
	}
	
	public function getCurrentInstanceURLWithParameters(){
		return $this->url;
	}
	
	public function getServerSwitch($instancePath = ''){
		if($this->serverSwitch==null){
			if(strlen($instancePath) > 0){
			    $this->serverSwitch=new XWServerSwitch($instancePath);
			}
			else{
			    $this->serverSwitch=new XWServerSwitch($this->getCurrentInstanceURLWithParameters());
			}		    
		}
		return $this->serverSwitch;
	}

	public function getEnvValues(){
	    $result = [];
	    try{
            $dir = new \DirectoryIterator($this->getCurrentInstanceDeploymentRootPath());
            foreach ($dir as $file){
                if(preg_match("/^env/i", $file->getFilename()) && preg_match("/\.json$/i", $file->getFilename())){
                    $env = json_decode(file_get_contents($file->getRealPath()), true);
                    $result = array_merge($result, $env);
                }
            }
        }
        catch(\Exception $e){

        }
        return $result;
    }
} 
