<?php
/*
 * Created on 17.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2011/2017 Hannes Pries <http://www.annonyme.de>
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

namespace core\modules;

use core\utils\XWArrayList;
use core\utils\config\GlobalConfig;
use xw\entities\users\XWUser;
use core\pages\XWCallableContent;

class XWModule implements XWCallableContent{
	
	private $name=""; //Für das Menü, label-name
	private $callName=""; //das was man im request als page angeben muss.. also ist sowas wie die id
	private $path="";
	private $instance=false; //im pages verzeichniss der instance oder global
	private $hidden=false;
	private $version="";
	private $hasOwnStyle=false;
	
	private $permissionGroups=null;
	
	private $nonTextPages=null;
	private $jsonPages=null;
	
	private $dictionaryPath="";
	
	private $adminGroup="";
	
	private $changeFrequence="weekly";

	private $requiered = [];
	
	private $deloymentSubPath ="";
	private $customDescriptorPath = "";
	
	public function __construct(){
		$this->permissionGroups=new XWArrayList();
		$this->nonTextPages=new XWArrayList();
		$this->jsonPages=new XWArrayList();
		
		$this->deloymentSubPath = GlobalConfig::instance()->getValue("moduledeployfolder");
		$this->deloymentSubPath .= GlobalConfig::instance()->getValue("moduledeployfile");
	}
	
	public function createSubMenuContainer(): XWModuleDeployer{
		$deploy=new XWModuleDeployer();	
		if($this->path!=""){
			$deploy->load($this);
		}		
		return $deploy;
	}
	
	public function hidde(){
	    $file = $this->path."/deploy/module.xml";
	    if(strlen($this->customDescriptorPath) > 0){
	        $file = $this->customDescriptorPath;
	    }
	    
	    if(is_file($file)){
	        $hidde="true";
	        if($this->hidden){
	            $hidde="false";
	        }
	        
	        $content=file_get_contents($file);
	        if(preg_match("/<hidden>.+<\/hidden>/Uis",$content)){
	            $content=preg_replace("/<hidden>.+<\/hidden>/Uis","<hidden>".$hidde."</hidden>",$content);
	        }
	        else{
	            $content=preg_replace("/<\/module>$/Uis","<hidden>".$hidde."</hidden>\n</module>",$content);
	        }
	        file_put_contents($file ,$content);
	    }
	}
	
	/** 
	 * @param XWUser $user
	 * @return boolean
	 */
	public function hasUserPermission(XWUser $user):bool{
		$found=false;
		$permSize=$this->permissionGroups->size();
		if($permSize==0){
			$found=true;
		}
		else if($user->getId()>0){
			for($i=0;$i<$permSize && !$found;$i++){
				if($user->isInGroup($this->permissionGroups->get($i))){
					$found=true;
					return $found;
				}
			}
		}
		
		return $found;
	}
	
	public function addPermission(string $permission){
		$this->permissionGroups->add($permission);
	}
	
	public function getSize(): int{
		return $this->permissionGroups->size();
	}
	
	public function getPermission(int $index): string{
		return $this->permissionGroups->get($index);
	}
	
	public function existsInNonTextPages(string $pageName): bool{
		$found=false;
		$size=$this->getNonTextPageListSize();
		for($i=0;$i<$size;$i++){
			if($this->getNonTextPage($i)==$pageName){
				$found=true;
				return $found;
			}
		}
		return $found;
	}
	
	public function addNonTextPage(string $pageName){
		$this->nonTextPages->add($pageName);
	}
	
	public function getNonTextPageListSize():int{
		return $this->nonTextPages->size();
	}
	
	public function getNonTextPage(int $index): string{
		return $this->nonTextPages->get($index);
	}
	
	public function existsInJsonPages(string $pageName): bool{
		$found=false;
		$size=$this->getJsonPageListSize();
		for($i=0;$i<$size;$i++){
			if($this->getJsonPage($i)==$pageName){
				$found=true;
				return $found;
			}
		}
		return $found;
	}
	
	public function addJsonPage(string $pageName){
		$this->jsonPages->add($pageName);
	}
	
	public function getJsonPageListSize():int {
		return $this->jsonPages->size();
	}
	
	public function getJsonPage(int $index): string{
		return $this->jsonPages->get($index);
	}
	
	public function getName(): string{
		return $this->name;
	}
	
	public function setName(string $name){
		$this->name=$name;
	}
	
	public function getCallName():string {
		return $this->callName;
	}
	
	public function setCallName(string $callName){
		$this->callName=$callName;
	}
	
	public function getPath():string {
		return $this->path;
	}
	
	public function setPath(string $path){
		$this->path=$path;
	}
	
	public function isInstance():bool {
		return $this->instance;
	}
	
	public function setInstance(bool $instance){
		$this->instance=$instance;
	}
	
	public function isHidden():bool{
		return $this->hidden;
	}
	
	public function setHidden(bool $hidden){
		$this->hidden=$hidden;
	}
	
	public function getDictionaryPath():string {
		return $this->dictionaryPath;
	} 
	
	public function setDictionaryPath(string $dictionaryPath){
		$this->dictionaryPath=$dictionaryPath;
	}
	
	public function getVersion():string{
		return $this->version;
	}
	
	public function setVersion(string $version){
		$this->version=$version;
	}
	
	public function getAdminGroup():string {
		return $this->adminGroup;
	}
	
	public function setAdminGroup(string $adminGroup){
		$this->adminGroup=$adminGroup;
	}
	
	public function getChangeFrequence():string {
		return $this->changeFrequence;
	}
	
	public function setChangeFrequence(string $changeFrequence){
		$this->changeFrequence=$changeFrequence;
	}
	
	public function isHasOwnStyle():bool {
		return $this->hasOwnStyle;
	}
	
	public function setHasOwnStyle(bool $hasOwnStyle){
		$this->hasOwnStyle=$hasOwnStyle;
	}

    public function getCustomDescriptorPath():string
    {
        return $this->customDescriptorPath;
    }

    public function setCustomDescriptorPath(string $customDescriptorPath)
    {
        $this->customDescriptorPath = $customDescriptorPath;
    }

     public function getParent() {      
         return "";
     }

    /**
     * @return array
     */
    public function getRequiered(): array
    {
        return $this->requiered;
    }

    /**
     * @param array $requiered
     */
    public function setRequiered(array $requiered)
    {
        $this->requiered = $requiered;
    }

    public function getRedirectLink()
    {
        return null;
    }
}
