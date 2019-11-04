<?php
namespace core\menu\renderers;

use core\menu\MenuRendererInterface;
use core\menu\MenuItem;
use core\net\XWRequest;

class MenuDefaultRenderer implements MenuRendererInterface{
	
	private $defaultClass = "pageMenuLink";
	private $currentClass = "pageMenuLinkCurrent active";
	
	public function __construct($defaultClass = "pageMenuLink", $currentClass = "pageMenuLinkCurrent active"){
		$this->defaultClass = $defaultClass;
		$this->currentClass = $currentClass;
	}
	/**
	 * @return bool
	 * @param MenuItem $item
	 */
	private function isCurrentPage($item){
		$result = true;
		if(!XWRequest::instance()->exists("page") || XWRequest::instance()->getString("page") != $item->getPageArgument()){
			$result = false;
		}
		else{
			if(!XWRequest::instance()->exists("sub") || XWRequest::instance()->getString("sub") != $item->getSubItems()){
				$result = false;
			}
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @param MenuItem[] $items
	 * {@inheritDoc}
	 * @see \core\menu\MenuRendererInterface::render()
	 */
	public function render($items = []) {
		$result = "<ul>\n";
		foreach ($items as $item){
			$class = $this->defaultClass;
			$href = "index.php?page=".$item->getPageArgument()."&sub=".$item->getSubArgument()."";
			if($item->isAdminPageArgument()){
				$href .= "&adminpage=1";
			}
			
			if($this->isCurrentPage($item)){
				$class = $this->currentClass;
			}
			$icon = "";
			if($item->getIconUrl() != ""){
				$icon = "<img src=\"images/".$item->getIconUrl()."\" alt=\"menuItemIcon\" class=\"menuItemIcon\"/>";
			}
			
			$result .= "<li><a href=\"".$href."\" class=\"".$class."\" >".$icon." ".$item->getName()."</a>\n";
			if(count($item->getSubItems()) > 0){
				$result .= $this->render($item->getSubItems());
			}
			$result .= "</li>\n";
		}
		$result .= "</ul>\n";
		return $result;
	}
}