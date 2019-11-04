<?php
namespace core\menu\renderers;

use core\menu\MenuRendererInterface;
use core\menu\MenuItem;
use core\net\XWRequest;

/**
 * https://getbootstrap.com/components/#navbar
 * @author hp@hannespries.de
 */
class MenuBootstrapNavBarRenderer implements MenuRendererInterface{
	
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
	 * {@inheritDoc}
	 * @see \core\menu\MenuRendererInterface::render()
	 */
	public function render($items = []) {
		$result = "<nav class=\"navbar navbar-default\">\n";
		$result .= "	<div class=\"container-fluid\">\n";
		
		//HEADER
		$result .= "
				<div class=\"navbar-header\">
			      <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#bs-example-navbar-collapse-1\" aria-expanded=\"false\">
			        <span class=\"sr-only\">Toggle navigation</span>
			        <span class=\"icon-bar\"></span>
			        <span class=\"icon-bar\"></span>
			        <span class=\"icon-bar\"></span>
			      </button>
			      <a class=\"navbar-brand\" href=\"index.php\">Home</a>
			    </div>\n
				";
		
		//MENU
		$result .= "		<div class=\"collapse navbar-collapse\" id=\"bs-example-navbar-collapse-1\">\n";
		$result .= $this->renderItems($items, false);
		$result .= "		</div>\n";
		
		$result .= "	</div>\n";
		$result .= "</nav>\n";
		return $result;
	}
	
	/**
	 * @return string
	 * @param MenuItem[] $items
	 * @param bool $isSub
	 */
	private function renderItems($items = [], $isSub = false){
		$result = "";
		if($isSub){
			$result = "<ul class=\"dropdown-menu\">\n";
		}
		else{
			$result = "<ul class=\"nav navbar-nav\">\n";
		}
		
		foreach ($items as $item){
			$class = "";
			if($this->isCurrentPage($item)){
				$class = "active";
			}
			$href = "index.php?page=".$item->getPageArgument()."&sub=".$item->getSubArgument()."";
			if($item->isAdminPageArgument()){
				$href .= "&adminpage=1";
			}
			
			$result .= "<li class=\"".$class."\"><a href=\"".$href."\">".$item->getName()."</a>";
			if(count($item->getSubItems()) > 0){
				$result .= $this->renderItems($item->getSubItems(), true);	
			}			
			$result .= "</li>";
		}
		$result .= "</ul>\n";
		return $result;
	}
}