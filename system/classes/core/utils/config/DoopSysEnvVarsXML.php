<?php
/*
 * Created on 11.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008 Hannes Pries <http://www.annonyme.de>
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

namespace core\utils\config;

use Exception;
 
class DoopSysEnvVarsXML{
  private $fileContent="";
  private $file="";

  public function __construct($file="addons/portableTheme/info.xml"){
     $this->file=$file;
     if(file_exists($this->file)){
       /*
       $fHandle=fopen($this->file,"r+");
       $this->fileContent="";
       $i=0;
       while(!feof($fHandle)){
         $this->fileContent.=fgets($fHandle);
         $i++;
       }
       fclose($fHandle);
       */
       
       try{
       	  $this->fileContent=file_get_contents($this->file);
       	  $this->fileContent=preg_replace("/\n/"," ",$this->fileContent);
       }
       catch(Exception $e){
       		echo "\nfile read error! [".$file.":".$e."]\n";
       }       
     }
     else{
             echo "\n<!-- file not found! [".$file."] -->\n";
     }
  }
  
  //29.08.2008
  public function existsEnvVar($tagname){
  	  if(preg_match("/".$tagname."/",$this->fileContent)){
      	  return true;
      }
      else{
      	  return false;
      }
  }

  public function getEnvVar($tagname){
      //first check.. 
      if(preg_match("/".$tagname."/",$this->fileContent)){
      	  return preg_replace("/^.*<".$tagname.">(.*)<\/".$tagname.">.*$/Uis","$1",$this->fileContent);
      }
      else{
      	  return "can't found tag (" . $tagname . ") in XML.";
      }      
  }
  
  public function setEnvVar($tagname,$value){
  	  if(preg_match("/".$tagname."/",$this->fileContent)){
      	  $this->fileContent=preg_replace("/<".$tagname.">(.*)<\/".$tagname.">/Uis","<".$tagname.">".$value."<\/".$tagname.">",$this->fileContent);
      	  return $value;
      }
      else{
      	  return "can't found tag (" . $tagname . ") in XML.";
      }
  }
  
  public function saveChanges(){
  	$fHandle=fopen($this->file,"x+");
  	fwrite($fHandle,$this->fileContent,strlen($this->fileContent));
  	fclose($fHandle);
  }

  public function getFile(){
      return $this->file;
  }

  public function setFile($file){
      $this->file=$file;
  }
}
