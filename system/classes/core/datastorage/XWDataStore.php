<?php

namespace core\datastorage;

/*
 * Created on 25.10.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 /*
  * Copyright (c) 2007/2010 Hannes Pries <http://www.annonyme.de>
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
   
class XWDataStore{
	
	//DataStore MetaData
	private $name="";
	private $type=""; //ftp or filesystem
	
	//ftp connection-data
	private $host="";
	private $user="";
	private $password="";
	
	//filesystem path or path relative to ftp home-folder
	private $savePath="";
	
	//fullpath for printing to html-pages
	private $loadFullPath="";
	//relative path (relative to html-page folder) for printing to html-pages
	private $loadRelativePath="";
	
	
	//runtime Attributes
	private $ftpConnection=null;
	
	
	public function __construct(){
		
	}	
	
	public function loadFileList(){
		return $this->loadFileListFromDataStore();
	}
	
	public function loadFileListFromDataStore(){
		$this->connectIfNeeded();
		$list=new XWFileList();
		if($this->type=="ftp"){
			$files=ftp_nlist($this->ftpConnection,$this->savePath);
			for($i=0;$i<count($files);$i++){
				$list->addFile($files[$i]);
			}
		}
		elseif($this->type=="filesystem"){			
			$di=new \DirectoryIterator($this->savePath);
			foreach($di as $file){
				if($file->isFile()){
					$list->addFile($file->getFilename());
				}
			}
		}
		$this->disconnectIfNeeded();
		return $list;
	}
	
	public function saveFileTo($filenameAndPath,$newFilename,$type=FTP_BINARY){
		$this->saveFileToDataStore($filenameAndPath,$newFilename,$type);
	}
	
	public function saveFileToDataStore($filenameAndPath,$newFilename,$type=FTP_BINARY){
		$this->connectIfNeeded();
		if($this->type=="ftp"){
			ftp_put($this->ftpConnection,$this->savePath.$newFilename,$filenameAndPath,$type);
			unlink($filenameAndPath);
		}
		elseif($this->type=="filesystem"){
			copy($filenameAndPath,$this->savePath.$newFilename);			
		}
		$this->disconnectIfNeeded();
	}
	
	public function appendFile($filename,$content){
		$this->appendFileInDataStore($filename,$content);
	}
	
	public function appendFileInDataStore($filename,$content){
		$this->connectIfNeeded();
		if($this->type=="ftp"){
			//ftp_put($this->ftpConnection,$this->savePath.$newFilename,$filenameAndPath,$type);
			//unlink($filenameAndPath);
		}
		elseif($this->type=="filesystem"){
			file_put_contents($this->savePath.$filename,$content,FILE_APPEND);			
		}
		$this->disconnectIfNeeded();
	}
	
	public function deleteFile($filename){
		$this->deleteFileFromDataStore($filename);
	}
	
	public function deleteFileFromDataStore($filename){
		$this->connectIfNeeded();
		if($this->type=="ftp"){
			ftp_delete($this->ftpConnection,$this->savePath.$filename);
		}
		elseif($this->type=="filesystem"){
			unlink($this->savePath.$filename);
		}
		$this->disconnectIfNeeded();
	}
	
	/*
	public function loadFileFromDataStore($filename,$newFilenameAndPath){
		$this->connectIfNeeded();
		$this->disconnectIfNeeded();
	}
	*/
	
	
	public function fileExists($filename){
		$exists=false;
		$this->connectIfNeeded();
		if($this->type=="ftp"){
			$exists=true; //nochmal besser überarbeiten
		}
		elseif($this->type=="filesystem"){
			$exists=file_exists($this->savePath.$filename);
		}
		$this->disconnectIfNeeded();
		return $exists;
	}
	
	/**
	 * 
	 */
	public function renameFile($oldFilename,$newFilename){
		$this->renameFileInDataStore($oldFilename,$newFilename);
	}
	
	public function renameFileInDataStore($oldFilename,$newFilename){
		$this->connectIfNeeded();
		if($this->type=="ftp"){
			ftp_rename($this->ftpConnection,$this->savePath.$oldFilename,$this->savePath.$newFilename);
		}
		elseif($this->type=="filesystem"){
			rename($this->savePath.$oldFilename,$this->savePath.$newFilename);
		}
		$this->disconnectIfNeeded();
	}
	
	public function createMD5HashFromFileInDataStore($filename){
		$result="";
		if($this->fileExists($filename)){
			$this->connectIfNeeded();
			if($this->type=="ftp"){
				//ftp_rename($this->ftpConnection,$this->savePath.$oldFilename,$this->savePath.$newFilename);
			}
			elseif($this->type=="filesystem"){
				$result=md5(file_get_contents($this->savePath.$filename));
			}
			$this->disconnectIfNeeded();
		}
		return $result;
	}
	
	public function createSubfolderIfNotExisting($folder){
		
	}
	
	public function copyFile($filename,$toFilename,$override){
		
	}
	
	//-----------
	private function connectIfNeeded(){
		if($this->type=="ftp"){
			$this->ftpconnection=ftp_connect($this->host);
			ftp_login($this->ftpconnection,$this->user,$this->password);
		}
	}
	
	private function disconnectIfNeeded(){
		if($this->type=="ftp"){
			ftp_close($this->ftpConnection);
		}
	}
	//-----------
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function setType($type){
		$this->type=$type;
	}
	
	public function getHost(){
		return $this->host;
	}
	
	public function setHost($host){
		$this->host=$host;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function setUser($user){
		$this->user=$user;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function setPassword($password){
		$this->password=$password;
	}
	
	public function getSavePath(){
		return $this->savePath;
	}
	
	public function setSavePath($savePath){
		$this->savePath=$savePath;
	}
	
	public function getLoadFullPath(){
		return $this->loadFullPath;
	}
	
	public function setLoadFullPath($loadFullPath){
		$this->loadFullPath=$loadFullPath;
	}
	
	public function getLoadRelativePath(){
		return $this->loadRelativePath;
	}
	
	public function setLoadRelativePath($loadRelativePath){
		$this->loadRelativePath=$loadRelativePath;
	}
} 
?>
