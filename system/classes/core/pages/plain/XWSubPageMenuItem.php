<?php
namespace core\pages\plain;

use core\utils\XWArrayList;
use core\pages\XWCallableContent;
/*
 * Created on 28.11.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 /*
  * Copyright (c) 2007/2011 Hannes Pries <http://www.annonyme.de>
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
 
class XWSubPageMenuItem implements XWCallableContent{
	private $label="";
	private $linkedPage="";	
	private $icon="";
	private $onlyVisibleWithLogin=false;
	private $moduleName = "";
	private $redirectLink = null;
	
	private $subItems=null;
	
	public function __construct(){
		$this->subItems=new XWArrayList();
	}
	
	public function addSubItem($item){
		$this->subItems->add($item);
	}
	
	public function addSubPageMenuItem(XWSubPageMenuItem $item){
		$this->subItems->add($item);
	}
	
	public function getSize(){
		return $this->subItems->size();
	}
	
	public function getSubItem($index):XWSubPageMenuItem{
		return $this->subItems->get($index);
	}
	
	public function getSubPageMenuItem($index){
		return $this->subItems->get($index);
	}
	
	public function getLabel(){
		return $this->label;
	}
	
	public function setLabel($label){
		$this->label=$label;
	}
	
	public function getLinkedPage(){
		return $this->linkedPage;
	}
	
	public function setLinkedPage($linkedPage){
		$this->linkedPage=$linkedPage;
	}
	
	public function getIcon(){
		return $this->icon;
	}
	
	public function setIcon($icon){
		$this->icon=$icon;
	}
	
	public function isOnlyVisibleWithLogin(){
		return $this->onlyVisibleWithLogin;
	}
	
	public function setOnlyVisibleWithLogin($onlyVisibleWithLogin){
		$this->onlyVisibleWithLogin=$onlyVisibleWithLogin;
	}
     /**
      * {@inheritDoc}
      * @see \core\pages\XWCallableContent::getName()
      */
     public function getName() {
        return $this->getLabel();
     }
    
     /**
      * {@inheritDoc}
      * @see \core\pages\XWCallableContent::getCallName()
      */
     public function getCallName() {
        return $this->getLinkedPage();
     }
    
     /**
      * {@inheritDoc}
      * @see \core\pages\XWCallableContent::getParent()
      */
     public function getParent() {
         return $this->getModuleName();
     }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    public function setRedirectLink($redirectLink = null)
    {
        $this->redirectLink = $redirectLink;
    }

    public function getRedirectLink()
    {
        return $this->redirectLink;
    }
}
