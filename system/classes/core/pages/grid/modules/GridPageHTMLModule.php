<?php
namespace core\pages\grid\modules;

use core\pages\grid\GridPageModule;
use core\pages\grid\GridPageModuleDescription;

class GridPageHTMLModule implements GridPageModule{
	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::render()
	 */
	public function render(GridPageModuleDescription $moduleDescription) {
		return $moduleDescription->getContent();
	}
	
	public function generateName(){
		return preg_replace("/[^a-zA-Z0-9]/", "_", get_class($this));
	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::renderJSController()
	 */
	public function renderJSController() {
		return "
			function (controller,http){
			    this.data=\"\";
			    this.show=\"none\";
			    this.controller=controller;
			    this.http=http;
				this.className=\"".$this->generateName()."\";
				this.name=\"HTML-Module\";						
			
			    this.getContent=function(){
			        return this.data;
			    };
			
			    this.renderPreview=function(item){
			        return item.description.content;
			    }
			}";
	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::renderEditForm()
	 */
	public function renderEditForm() {
		return GridPageDefaultModuleToolKit::generateDialog("<textarea id=\"content-edit\" ng-model=\"selectedModule.data\" placeholder=\"HTML-Code...\"></textarea>", $this->generateName());
	}
	/**
	 * {@inheritDoc}
	 * @see \core\pages\grid\GridPageModule::isDefault()
	 */
	public function isDefault() {
		return true;
	}

}