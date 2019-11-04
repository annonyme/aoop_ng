<?php
namespace core\pages\plain;

use core\utils\config\DoopSysEnvVarsXML;
use core\pages\XWCallableContent;
use xw\entities\users\XWUser;
/*
 * Created on 03.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2015 Hannes Pries <http://www.annonyme.de>
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
 
class XWPage implements XWCallableContent{
	
	private $name = "";
	private $callName="";
	private $path="";
	private $content="";
	private $contentType="plain";
	private $hidden=false;
	private $backup=false;
	
	private $orderIndex=0;
	
	private $multipage=false; //TODO remove
	private $initPage=false; //TODO remove
	
	private $parentPage="";
	private $collection=[]; //only if parent page
	
	private $metaDataFileExists=false;
	private $folder="";
	
	private $userId=0;
	
	private $dictionaryPath="";
	private $restriction="";
	
	private $metaDescription="";
	
	private $values = [];
	
	private $adminPage = false;
	
	private $link = "";
	
	public function __construct(){
		
	}
	
	/**
	 * Check if restirction ist setted  and if user is accepted
	 * @param XWUser $user
	 */
	public function checkRestriction($user){
		$result=true;
		if(strlen($this->restriction)>0){
			if($user!=null && $user->getId()>0){
				$result=$user->isInGroup($this->restriction);
			}
		}
		return $result;
	}
	
	public function load($callName, $folder , $locale=""){
		if(!preg_match("/\/$/",$folder)){
			$folder.="/";
		}
		
		$locale=strtolower($locale);
		$localeExt="";
		if(is_file($folder.$callName."_".$locale.".html")){
			$localeExt="_".$locale;
		}
		
		if(is_file($folder.$callName.$localeExt.".html")){
			$this->path=$folder.$callName.$localeExt.".html";
			$this->callName=$callName;
			$this->folder=$folder;
			
			
			//hier metaDataFile lesen
			if(is_file($folder.$callName.".xml")){
				$this->metaDataFileExists=true;
				$xmlReader=new DoopSysEnvVarsXML($folder.$callName.".xml");
				$this->contentType=$xmlReader->getEnvVar("contentType");				
				if($xmlReader->existsEnvVar("name".$localeExt)){					
					$this->name=$xmlReader->getEnvVar("name".$localeExt);
				}
				else{
					$this->name=$xmlReader->getEnvVar("name");
				}
				
				$this->callName=$callName;
				if($xmlReader->getEnvVar("hidden")=="true"){
					$this->hidden=true;
				}
				$this->userId=$xmlReader->getEnvVar("userId");
				
				if($xmlReader->existsEnvVar("parent")){
					$val = $xmlReader->getEnvVar("parent");
				    $this->parentPage = strlen(trim($val)) > 0 ? trim($val) : null;
				}
				else{
					$this->parentPage = "";
				}
				
				if($xmlReader->existsEnvVar("orderIndex")){
					$this->orderIndex=(int) $xmlReader->getEnvVar("orderIndex");
				}
				
				if($xmlReader->existsEnvVar("link")){
				    $this->link=$xmlReader->getEnvVar("link");
				}
				
				if($xmlReader->existsEnvVar("metaDescription")){
					$this->metaDescription=$xmlReader->getEnvVar("metaDescription");
				}
				
				if($xmlReader->existsEnvVar("values")){
				    $this->values=json_decode($xmlReader->getEnvVar("values"), true);
				}
				
				if($xmlReader->existsEnvVar("dictionary")){
					$this->dictionaryPath=$folder.$xmlReader->getEnvVar("dictionary");
				}
				if($xmlReader->existsEnvVar("restriction")){
					$this->restriction=$folder.$xmlReader->getEnvVar("restriction");
				}
			}
			
			$this->content=file_get_contents($this->path);			
		}
		elseif(file_exists($folder.$callName.$localeExt.".bkup")){				
			//neues backup/history format
			$this->folder=$folder;
			$this->path=$folder.$callName.$localeExt.".bkup";
			$this->hidden=true;
			$this->callName=$callName;
			$this->contentType="plain";
			$this->metaDataFileExists=false;
			$this->backup=true;
			
			$this->content=file_get_contents($this->path);
		}
		else{
			$this->path="";
		}
		
		if(strlen($this->name) == 0){
		    $this->name = $this->callName;
		}
	}
	
	public function save($dontBackup=false){
		if(!$dontBackup){
			$this->saveAsBackup($this->folder);
		}
		
		$fhandle=fopen($this->path,"w+");
	    fwrite($fhandle,$this->content);
	    fclose($fhandle);
	    
	    //xml neu erstellen
	    $hidden="false";
	    if($this->hidden){
	    	$hidden="true";
	    }
	    
	    if($this->getName()==""){
	    	$this->setName($this->callName);
	    }
	    
	    if(file_exists(preg_replace("/htm(l)?$/i","xml",$this->path))){
	    	//�berschreiben
	    	$xml="<page>\n" .
	    		 "  <name>".$this->name."</name>\n" .
	    		 "  <hidden>".$hidden."</hidden>\n" .
	    		 "  <contentType>".$this->contentType."</contentType>\n" .
	    		 "  <userId>".$this->userId."</userId>\n" .
	    		 "  <link>".$this->link."</link>\n" .
	    		 "  <draft>false</draft>\n" .
	    		 "  <lastEdit>".date("Y/m/d h:i:s",time())."</lastEdit>\n" .
	    		 "  <parent>".$this->parentPage."</parent>\n" .
	    		 "  <values>".json_encode($this->values)."</values>\n" .
	    		 "  <metaDescription>".$this->metaDescription."</metaDescription>\n";
	    	if($this->dictionaryPath!=""){
	    		$xml.="<dictionary>".$this->dictionaryPath."</dictionary>\n";
	    	}	
	    	if($this->restriction!=""){
	    		$xml.="<restriction>".$this->restriction."</restriction>\n";
	    	}
	    	$xml.="</page>";
	    		 
	    	$fhandle=fopen(preg_replace("/html(l)?$/i","xml",$this->path),"w+");
	        fwrite($fhandle,$xml);
	        fclose($fhandle);	 
	    }
	    else{
	    	//neu anlegen
	    	$xml="<page>\n" .
	    		 "  <name>".$this->name."</name>\n" .
	    		 "  <hidden>".$hidden."</hidden>\n" .
	    		 "  <contentType>".$this->contentType."</contentType>\n" .
	    		 "  <userId>".$this->userId."</userId>\n" .
	    		 "  <link>".$this->link."</link>\n" .
	    		 "  <draft>false</draft>\n" .
	    		 "  <lastEdit>".date("Y/m/d h:i:s",time())."</lastEdit>\n" .
	    		 "  <parent>".$this->parentPage."</parent>\n" .
	    		 "  <values>".json_encode($this->values)."</values>\n" .
	    		 "  <metaDescription>".$this->metaDescription."</metaDescription>\n";
	    	if($this->dictionaryPath!=""){
	    		$xml.="<dictionary>".$this->dictionaryPath."</dictionary>\n";
	    	}	
	    	if($this->restriction!=""){
	    		$xml.="<restriction>".$this->restriction."</restriction>\n";
	    	}
	    	$xml.="</page>";
	    		 
	    	$fhandle=fopen(preg_replace("/htm(l)?$/i","xml",$this->path),"x");
	        fwrite($fhandle,$xml);
	        fclose($fhandle);
	    }
	}
	
	public function saveAsBackup($folder){
		$newCallName=$this->callName."-".date("Y_m_d_h_i_s",time())."-".$this->userId.".bkup";
		if(file_exists($folder) && !$this->backup){
			$fhandle=fopen($folder.$newCallName,"x");
			$oldPage=new XWPage();
			$oldPage->load($this->callName,$this->folder);				        
	        fwrite($fhandle,$oldPage->getContent(),strlen($oldPage->getContent()));
	        fclose($fhandle);
		}
	}
	
	public function delete($dontBackup=false){
		if(!$dontBackup){
			$this->saveAsBackup($this->folder);
		}
		
		if(file_exists($this->path)){
			//unlink($this->path);
			rename($this->path,$this->path.".".time().".del");
		}		
		
		$sideXML=preg_replace("/html(l)?$/","xml",$this->path);
		if(file_exists($sideXML)){
			//unlink(preg_replace("/html(l)?$/","xml",$this->path));
			rename($sideXML,$sideXML.".".time().".del");
		}		
	}
	
	public function addCollectionPage($page){
		$this->collection[count($this->collection)]=$page;
	}
	
	public function getSize(){
		return count($this->collection);
	}
	
	public function getCollectionPage($index){
		return $this->collection[$index];
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	public function getCallName(){
		return $this->callName;
	}
	
	public function setCallName($callName){
		$this->callName=$callName;
	}
	
	public function getPath(){
		return $this->path;
	}
	
	public function setPath($path){
		return $this->path=$path;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function setContent($content){
		$this->content=$content;
	}
	
	public function getContentType(){
		return $this->contentType;
	}
	
	public function setContentType($contentType){
		$this->contentType=$contentType;
	}
	
	public function isHidden(){
		return $this->hidden;
	}
	
	public function setHidden($hidden){
		$this->hidden=$hidden;
	}
	
	public function isBackup(){
		return $this->backup;
	}
	
	public function setBackup($backup){
		$this->backup=$backup;
	}
	
	public function isMetaDataFileExists(){
		return $this->metaDataFileExists;
	}
	
	public function setMetaDataFileExists($metaDataFileExists){
		$this->metaDataFileExists=$metaDataFileExists;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function setUserId($userId){
		$this->userId=$userId;
	}
	
	public function isMultiPage(){
		return $this->multipage;
	}
	
	public function setMultiPage($multiPage){
		$this->multipage=$multiPage;
	}
	
	public function isInitPage(){
		return $this->initPage;
	}
	
	public function setInitPage($initPage){
		$this->initPage=$initPage;
	}
	
	public function getParentPage(){
		return $this->parentPage;
	}
	
	public function setParentPage($parentPage){
		$this->parentPage=$parentPage;
	}
	
	public function getDictionaryPath(){
		return $this->dictionaryPath;
	} 
	
	public function setDictionaryPath($dictionaryPath){
		$this->dictionaryPath=$dictionaryPath;
	}
	
	public function getOrderIndex(){
		return $this->orderIndex;
	}
	
	public function setOrderIndex($orderIndex){
		$this->orderIndex=$orderIndex;
	}
	
	public function getRestriction() {
		return $this->restriction;
	}
	
	public function setRestriction($restriction) {
		$this->restriction = $restriction;
		return $this;
	}
	
	public function getMetaDescription() {
		return $this->metaDescription;
	}
	
	public function setMetaDescription($metaDescription) {
		$this->metaDescription = $metaDescription;
	}

     /**
      * {@inheritDoc}
      * @see \core\pages\XWCallableContent::getParent()
      */
     public function getParent() {
         return $this->getParentPage();
     }

     public function isAdminPage()
     {
        return $this->adminPage;
     }

     public function setAdminPage($adminPage)
     {
        $this->adminPage = $adminPage;
     }

     public function getValues():array
     {
        return $this->values;
     }

     public function setValues(array $values)
     {
        $this->values = $values;
     }
    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getRedirectLink()
    {
        return $this->getLink();
    }
} 
