<?php 

namespace core\utils;

use DOMDocument;
use core\utils\config\GlobalConfig;
	
/* 
 * Created on 03.05.2007 
 * 
 * To change the template for this generated file go to 
 * Window - Preferences - PHPeclipse - PHP - Code Templates 
 */ 
  
 /* 
  * Copyright (c) 2007/2008/2011/2015 Hannes Pries <http://www.annonyme.de> 
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
 
 
//Forke of the DoopServerSwitch 
 class XWServerSwitch{ 
 
         private $url=""; 
         private $pages="servers/default/pages/"; 
         private $theme="xwtemp"; 
         private $adminTheme=""; 
         private $name="default"; 
         private $homepage="home"; 
         private $addons="addons/"; 
         private $globalModuleDir = "modules/"; 
         private $admins="admins"; 
         private $keywords="aoop"; //for metatag 
         private $dbname="embdoop"; 
 
         public function __construct($url=""){              
             $path="";
             if(!preg_match("/^path:/", $url)){
                $configFile = GlobalConfig::instance()->getValue("configspath")."servers";                
                
                if(is_file($configFile.".json")){
                    $json=json_decode(file_get_contents($configFile.".json"),true);
                    $count=count($json["domains"]);
                    for($i=0;$i<$count && strlen($path)==0;$i++){
                        $domain=$json["domains"][$i];
                        if(preg_match($domain["pattern"], $url)){
                            $path=trim($domain["folder"]);
                        }
                    }
                }
                else if(is_file($configFile.".xml")){
                    $doc = new DOMDocument();
                    $doc->load($configFile.".xml");
                    
                    $domains=$doc->getElementsByTagName("domain");
                    foreach ($domains as $domain){
                        $children=$domain->childNodes;
                        $found=false;
                        $checkPath="";
                        foreach($children as $child){
                            if($child->nodeName=="pattern"){
                                $found=preg_match($child->nodeValue,$url);
                            }
                            else if($child->nodeName=="folder"){
                                $checkPath=$child->nodeValue;
                            }
                        }
                        
                        if($found){
                            $path=$checkPath;
                            break;
                        }
                    }
                }                
            }
            else{
                $path = preg_replace("/^path:/", '', $url);
            }
            
            if(strlen($path)>0){
                $this->name=$path;
                $this->url=$url;
                
                $deployerPath=GlobalConfig::instance()->getValue("instancesfolder").$path."/deploy.xml";
                
                $inst= new DOMDocument();
                $inst->load($deployerPath);
                $roots=$inst->getElementsByTagName("server");
                $rChildren=$roots->item(0)->childNodes;
                
                $this->pages = GlobalConfig::instance()->getValue("instancesfolder").$path ."/pages/";
                foreach($rChildren as $rChild){
                    if($rChild->nodeName=="template"){
                        $this->theme=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="homepage"){
                        $this->homepage=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="pages"){
                        $this->pages=GlobalConfig::instance()->getValue("instancesfolder").$path."/".$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="addons"){
                        $this->addons=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="admins"){
                        $this->admins=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="keywords"){
                        $this->keywords=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="dbname"){
                        $this->dbname=$rChild->nodeValue;
                    }
                    else if($rChild->nodeName=="admintheme"){
                        $this->adminTheme=$rChild->nodeValue;
                    }
                }
            }
         } 
          
         public function save(){                 
             //deploy.xml  laden... 
             $deployerPath="servers/".$this->name."/deploy.xml"; 
              
             $xml=file_get_contents($deployerPath); 
              
             $xml=preg_replace("/<template>(.*)<\/template>/Uis","<template>".$this->theme."</template>",$xml); 
             $xml=preg_replace("/<pages>(.*)<\/pages>/Uis","<pages>".$this->pages."</pages>",$xml); 
             $xml=preg_replace("/<homepage>(.*)<\/homepage>/Uis","<homepage>".$this->homepage."</homepage>",$xml); 
             $xml=preg_replace("/<addons>(.*)<\/addons>/Uis","<addons>".$this->addons."</addons>",$xml); 
             $xml=preg_replace("/<admins>(.*)<\/admins>/Uis","<admins>".$this->admins."</admins>",$xml); 
             $xml=preg_replace("/<filecreatingdate>(.*)<\/filecreatingdate>/Uis","<filecreatingdate>".date("Y.m.d",time())."</filecreatingdate>",$xml); 
 
 
       if(preg_match("/<keywords>/is",$xml)){ 
         $xml=preg_replace("/<keywords>(.*)<\/keywords>/Uis","<keywords>".$this->keywords."</keywords>",$xml); 
       } 
       else{ 
         $xml=preg_replace("/<\/server>/Uis","<keywords>".$this->keywords."</keywords>\n</server>",$xml); 
       } 
        
       if(preg_match("/<dbname>/is",$xml)){ 
         $xml=preg_replace("/<dbname>(.*)<\/dbname>/Uis","<dbname>".$this->dbname."</dbname>",$xml); 
       } 
       else{ 
         $xml=preg_replace("/<\/server>/Uis","<dbname>".$this->dbname."</dbname>\n</server>",$xml); 
       } 
                           
             if($fHandle=fopen($deployerPath,"w")){ 
                  fwrite($fHandle,$xml); 
                  fclose($fHandle); 
             }                                             
         } 
          
         public function saveSingleValueToDescriptor($key,$value){ 
            $deployerPath="servers/".$this->name."/deploy.xml"; 
              
             $xml=file_get_contents($deployerPath); 
 
       if(preg_match("/<".$key.">/is",$xml)){ 
         $xml=preg_replace("/<".$key.">(.*)<\/".$key.">/Uis","<".$key.">".$value."</".$key.">",$xml); 
       } 
       else{ 
         $xml=preg_replace("/<\/server>/Uis","<".$key.">".$value."</".$key.">\n</server>",$xml); 
       } 
                           
             if($fHandle=fopen($deployerPath,"w")){ 
                  fwrite($fHandle,$xml); 
                  fclose($fHandle); 
             }  
         } 
 
         public function getTheme(){ 
                 return $this->theme; 
         } 
 
         public function setTheme($theme){ 
                 $this->theme=$theme; 
         } 
 
         public function getPages(){ 
                 return $this->pages; 
         } 
 
         public function setPages($pages){ 
                 $this->pages=$pages; 
         } 
 
         public function getHomepage(){ 
                 return $this->homepage; 
         } 
 
         public function setHomepage($homepage){ 
                 $this->homepage=$homepage; 
         } 
 
         public function getName(){ 
                 return $this->name; 
         } 
 
         public function setName($name){ 
                 $this->name=$name; 
         } 
 
         public function getAddons(){ 
             return $this->addons; 
         } 
 
         public function setAddons($addons){ 
             $this->addons=$addons; 
         } 
 
         public function getAdmins(){ 
             return $this->admins; 
         } 
 
         public function setAdmins($admins){ 
            $this->admins=$admins; 
         } 
          
         public function getKeywords(){ 
           return $this->keywords; 
         } 
          
         public function setKeywords($keywords){ 
           $this->keywords=$keywords; 
         } 
          
         public function getDbname(){ 
           return $this->dbname; 
         } 
          
         public function setDbname($dbname){ 
           $this->dbname=$dbname; 
         } 
          
         public function getAdminTheme(){ 
           return $this->adminTheme; 
         } 
          
         public function setAdminTheme($adminTheme){ 
           $this->adminTheme=$adminTheme; 
         } 
    public function getGlobalModuleDir() { 
      return $this->globalModuleDir; 
    } 
    public function setGlobalModuleDir($globalModuleDir) { 
      $this->globalModuleDir = $globalModuleDir; 
    } 
 }