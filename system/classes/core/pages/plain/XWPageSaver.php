<?php
namespace core\pages\plain;

/*
 * Created on 05.10.2007
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
 
use core\utils\filesystem\XWFileList;
use core\utils\filesystem\XWSimpleFileReader;
 
//perform stripslashes before add content to object... 
class XWPageSaver{
	private $path="";
	private $file="";
	private $currentContent="";
	private $newContent="";
	private $newFileName="";
	private $userId=0;
	
	private $oldVersions=[];
	
	public function __construct($path,$file){
		$this->path=$path;
		$this->file=$file;
		$sr=new XWSimpleFileReader($path.$file);
		$this->currentContent=$sr->getContent();
		
		$this->loadOldVersionList();
	}
	
	public function saveNewContent($newContent="",$userId=0){
	    $this->setNewContent($newContent);
	    $this->setUserId($userId);
	    return $this->save();	
	}
	
	public function save(){
		if($this->newContent!=""){
			//save old Version
			$this->createNewFileName();
		    $fhandle=fopen($this->path.$this->newFileName,"x");	        
	        fwrite($fhandle,$this->currentContent,strlen($this->currentContent));
	        fclose($fhandle);
	    
	        //save Page
	        $fhandle=fopen($this->path.$this->file,"w+");
	        fwrite($fhandle,$this->newContent);
	        fclose($fhandle);
	    
	        return true;
		}
		else{
			return false;
		}		
	}
	
	private function loadOldVersionList(){
		//-- oldVersions-List
		$fileList=new XWFileList();
		$fileList->load($this->path);
		for($i=0;$i<$fileList->getSize();$i++){
			if(preg_match("/^".$this->file.".+\.bkup$/Uis",$fileList->getFile($i))){
				$this->addOldVersion($fileList->getFile($i));
			}
		}
	}
	
	private function createNewFileName(){
		//userId ist immer die des users
		//der die neuere Version erstellt hat.. also nicht
		//der die Version in der backup-datei erstellt hat,
		//sondern der der die backup-datei erstellt hat..
		$newName="";
		//$newName.=preg_replace("/\.html/","",$this->file);
		$newName.=$this->file;
		$newName.="-".date("Y_m_d_h_i_s",time());
		$newName.="-".$this->userId.".bkup";
		$this->newFileName=$newName;
	}
	
	//---
	
	public function addOldVersion($oldVersion){
		$this->oldVersions[count($this->oldVersions)]=$oldVersion;
	}
	
	public function getSize(){
		return count($this->oldVersions);
	}
	
	public function getOldVersion($index){
		return $this->oldVersions[$index];
	}
	
	//---
	public function getUserId(){
		return $this->userId;
	}
	
	public function setUserId($userId){
		$this->userId=$userId;
	}
	
	public function getNewContent(){
		return $this->newContent;
	}
	
	public function setNewContent($newContent){
		$this->newContent=$newContent;
	}
	
	public function getCurrentContent(){
		return $this->currentContent;
	}
	
	private function setCurrentContent($currentContent){
		$this->currentContent=$currentContent;
	}	
}
