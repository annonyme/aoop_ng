<?php
namespace core\pages\grid\modules;

use core\pages\grid\GridPageModule;
use core\pages\grid\GridPageModuleDescription;

class GridPageMediaModule implements GridPageModule{
	
	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::render()
	 */
	public function render(GridPageModuleDescription $moduleDescription) {
		$result = "";
		$content = $moduleDescription->getContent();
		// {url,type,link,newtab,title}
		if($content && is_array($content) && isset($content["url"]) && strlen(trim($content["url"])) > 0){
			$html = "";
			
			$mediaHtml = "";
			if(isset($content["type"]) && $content["type"] == "video"){
				$mediaHtml = "<video src=\"".trim($content["url"])."\" class=\"media-video\" mute autoplay loop>\n";
			}
			else{
				$mediaHtml = "<img src=\"".trim($content["url"])."\" class=\"media-image\">\n";
			}
			
			$titleCouldBeLink = isset($content["title"]) && strlen(trim($content["title"])) > 0;
			
			if($titleCouldBeLink){
				$html .= $mediaHtml;
			}			
			if(isset($content["link"]) && strlen(trim($content["link"])) > 0){
				$html .= "<a href=\"".trim($content["link"])."\" target=\"".(isset($content["newtab"]) ? trim($content["newtab"]) : "" )."\">\n";
			}			
			if($titleCouldBeLink){
				$html = "<span class=\"media-title\">".trim($content["title"])."</span>\n";
			}
			else{
				$html .= $mediaHtml;
			}			
			if(isset($content["link"]) && strlen(trim($content["link"])) > 0){
				$html .= "</a>\n";
			}			
			
			$result = $html;
		}		
		return $result;
	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::renderJSController()
	 */
	public function renderJSController() {
		return "
			function (controller,http){
			    this.data={};
			    this.show=\"none\";
			    this.controller=controller;
			    this.http=http;
				this.className=\"".$this->generateName()."\";
				this.name=\"Image/Video-Module\";						
			
			    this.getContent=function(){
			        return this.data;
			    };
			
			    this.renderPreview=function(item){
			        var out = \"\";
					if(item.description.content.url && item.description.content.url.length>0){	
						if(item.description.content.title){
							out += item.description.content.title + \"<br>\";
						}						
						if(item.description.content.type == \"video\"){
							out += '<video src=\"'+item.description.content.url+'\" class=\"media media-video\" mute autoplay loop>';
						}
						else{
							out += '<img src=\"'+item.description.content.url+'\" class=\"media media-image\">';
						}
					}
					else{
						out = \"missing media-url\";	
					}	
					return out;
			    }
			}";
	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::renderEditForm()
	 */
	public function renderEditForm() {
		$form = "
				<input type=\"url\" placeholder=\"Media-URL...\" maxlength=\"255\" ng-model=\"selectedModule.data.url\"><br/>\n
				<input type=\"text\" placeholder=\"Title...\" maxlength=\"255\" ng-model=\"selectedModule.data.title\"><br/>\n
				Type: <select ng-model=\"selectedModule.data.type\">\n
					<option value=\"image\">Image (jpeg, png, gif)</option>\n
					<option value=\"video\">Video (mp4, webm, ogv)</option>\n
				</select><br>\n
				open in new tab: <select ng-model=\"selectedModule.data.newtab\">\n
					<option value=\"\">no</option>\n
					<option value=\"_blank\">yes</option>\n
				</select>\n
				";
		
		return GridPageDefaultModuleToolKit::generateDialog($form, $this->generateName());

	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::generateName()
	 */
	public function generateName() {
		return preg_replace("/[^a-zA-Z0-9]/", "_", get_class($this));
	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::isDefault()
	 */
	public function isDefault() {
		return false;
	}

}