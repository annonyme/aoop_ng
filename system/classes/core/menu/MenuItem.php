<?php
namespace core\menu;

class MenuItem{
	private $name = "";
	private $title = "";
	
	private $orderGroup = "";
	
	private $iconUrl = "";
	
	private $pageArgument = "";
	private $subArgument = null;
	private $adminPageArgument = false;
	private $alternativeURL = "";
	
	private $onlyLoggedIn = false;
	private $userGroups = [];
	
	private $target = "";
	
	private $cssClasses = [];
	
	private $subItems = [];
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getTitle() {
		return $this->title;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getIconUrl() {
		return $this->iconUrl;
	}
	public function setIconUrl($iconUrl) {
		$this->iconUrl = $iconUrl;
	}
	public function getPageArgument() {
		return $this->pageArgument;
	}
	public function setPageArgument($pageArgument) {
		$this->pageArgument = $pageArgument;
	}
	/**
	 * @return MenuItem[]
	 */
	public function getSubArgument() {
		return $this->subArgument;
	}
	/**
	 * @param MenuItem[] $subArgument
	 */
	public function setSubArgument($subArgument) {
		$this->subArgument = $subArgument;
	}
	public function getAlternativeURL() {
		return $this->alternativeURL;
	}
	public function setAlternativeURL($alternativeURL) {
		$this->alternativeURL = $alternativeURL;
	}
	public function getTarget() {
		return $this->target;
	}
	public function setTarget($target) {
		$this->target = $target;
	}
	public function getCssClasses() {
		return $this->cssClasses;
	}
	public function setCssClasses($cssClasses) {
		$this->cssClasses = $cssClasses;
	}
	public function getSubItems() {
		return $this->subItems;
	}
	public function setSubItems($subItems) {
		$this->subItems = $subItems;
	}
	public function isAdminPageArgument() {
		return $this->adminPageArgument;
	}
	public function setAdminPageArgument($adminPageArgument) {
		$this->adminPageArgument = $adminPageArgument;
	}
	public function isOnlyLoggedIn() {
		return $this->onlyLoggedIn;
	}
	public function setOnlyLoggedIn($onlyLoggedIn) {
		$this->onlyLoggedIn = $onlyLoggedIn;
	}
	public function getUserGroups() {
		return $this->userGroups;
	}
	public function setUserGroups($userGroups) {
		$this->userGroups = $userGroups;
	}
	public function getOrderGroup() {
		return $this->orderGroup;
	}
	public function setOrderGroup($orderGroup) {
		$this->orderGroup = $orderGroup;
	}
}