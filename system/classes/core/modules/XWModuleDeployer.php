<?php
/*
 * Created on 16.09.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2011/2018 Hannes Pries <http://www.annonyme.de>
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

use DOMDocument;
use core\utils\config\GlobalConfig;
use core\pages\plain\XWSubPageMenuItem;
 
class XWModuleDeployer{
	private $list=[];
	private $deloymentSubPath = "";
	
	public function __construct(){
		$this->deloymentSubPath = GlobalConfig::instance()->getValue("moduledeployfolder");
		$this->deloymentSubPath .= GlobalConfig::instance()->getValue("moduledeployfile");
	}
	
	public function load(XWModule $module){
		$deploymentDescriptorFile=$this->deloymentSubPath;		
		if($module!=null && file_exists($module->getPath()."/".$deploymentDescriptorFile)){              
                
            $doc = new DOMDocument();
            $doc->load($module->getPath()."/".$deploymentDescriptorFile);
            
            $menuParents=$doc->getElementsByTagName("pagemenue");
            foreach($menuParents as $parent){
            	$this->recursiveItemCreation($parent, $this, $module);
            }               
		}
	}
	
	private function recursiveItemCreation($parentNode, $parentItem, XWModule $module){
		$nodes=$parentNode->childNodes;
		/** @var \DOMElement $node */
        foreach($nodes as $node){
			if($node->nodeName=="page" || $node->nodeName=="menuitem"){
				$item=new XWSubPageMenuItem();
				$item->setModuleName($module->getCallName());
                $item->setLinkedPage($node->nodeValue);
                $attrs=$node->attributes;
                foreach($attrs as $attr){
                	if($attr->name=="icon"){
                		$item->setIcon(trim($attr->value));
                	}
                	else if($attr->name=="callname"){
                		$item->setLinkedPage(trim($attr->value));
                	}
                	else if($attr->name=="title"){
                		$item->setLabel(trim($attr->value));
                	}
                	else if($attr->name=="onlyvisiblewithlogin"){
                		$item->setOnlyVisibleWithLogin(strtolower(trim($attr->value))=="true");
                	}
                    else if($attr->name=="redirect"){
                        $item->setRedirectLink(trim($attr->value));
                    }
                }
                
                if($node->hasChildNodes()){
                	$item=$this->recursiveItemCreation($node,$item, $module);
                }
                    
                if($item->getLinkedPage()!='' && !preg_match("/^\s*$/",$item->getLinkedPage()) && !preg_match("/^\s*$/",$item->getLabel()) && $item->getLabel()!=""){
                    $parentItem->addSubPageMenuItem($item);
                }
			}
		}
		return $parentItem;
	}
	
	public function addSubPageMenuItem(XWSubPageMenuItem $item){
		$this->list[count($this->list)]=$item;
	}
	
	public function getSize(){
		return count($this->list);
	}
	
	/**
	 * @return XWSubPageMenuItem
	 * @param int $index
	 */
	public function getSubPageMenuItem(int $index): XWSubPageMenuItem{
		return $this->list[$index];
	} 
} 
