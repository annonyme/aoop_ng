<?php
namespace core\pages\grid;

class GridPageRenderer{
	/**
	 * @return string
	 * @param GridPage $page
	 */
	public static function render($page){
		$result = "<div id=\"grid_".$page->getCallName()."\" class=\"container\">\n";
		try{
			foreach($page->getModules() as $ref){
				$mod = new GridPageModuleDescription($ref);
				if($mod->isValid()){
					$styles = array_merge($mod->getStyle(), $mod->getReference()->getStyle());
					$module = "		<div class=\"col-md-".$mod->getReference()->getWidth()." ".implode(" ", $styles)."\">\n";
					$module .= $mod->render();
					$module .= "	</div>\n";
					
					$result .= $module;
				}				
			}
		}
		catch(\Exception $e){
			
		}
		$result .= "</div>\n";
		
		return $result;
	}
}