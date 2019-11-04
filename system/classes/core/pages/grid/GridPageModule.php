<?php
namespace core\pages\grid;

interface GridPageModule{
	/**
	 * @return string
	 * @param GridPageModuleDescription $moduleDescription
	 */
	public function render(GridPageModuleDescription $moduleDescription);
	
	/**
	 * @return string
	 */
	public function renderJSController();
	
	/**
	 * @return string
	 */
	public function renderEditForm();
	
	/**
	 * @return string
	 */
	public function generateName();
	
	/**
	 * @return bool
	 */
	public function isDefault();
}