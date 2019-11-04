<?php
namespace core\pages\grid\modules;

use core\pages\grid\GridPageModuleDescription;

class GridPageSingleModuleRenderer{
	public static function render($pagefolder, $id, $width, $classes = []){
		$mod = new GridPageModuleDescription();
		if(!preg_match("/\/$/", $pagefolder)){
			$pagefolder .= "/";
		}
		$mod->load($pagefolder."pagemodules/".$id.".grid.module.json");
		$styles = array_merge($mod->getStyle(), $classes);
		$module = "		<div class=\"col-md-".$width." ".implode(" ", $styles)."\">\n";
		$module .= $mod->render();
		$module .= "	</div>\n";
			
		return $module;		
	}
}