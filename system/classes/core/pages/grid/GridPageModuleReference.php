<?php
namespace core\pages\grid;

class GridPageModuleReference{
	
	private $id = "";
	
	private $width = 1;
	private $style = [];
	
	private $description = null;
	
	private $page = null;
	
	/**
	 * @param array $moduleDescription
	 * @param GridPage $page
	 */
	public function __construct($moduleDescription, $page){
		$this->page = $page;
		
		if(isset($moduleDescription["id"]) && strlen($moduleDescription["id"])>0){
			$this->id = $moduleDescription["id"];
			$this->description = new GridPageModuleDescription($this);
		}
		else{
			$this->id = "m".time();
			$this->description = new GridPageModuleDescription();
		}
		
		if(isset($moduleDescription["description"]) && is_array($moduleDescription["description"])){
			$this->description->loadByJson($moduleDescription["description"], $this);
		}
		
		if(isset($moduleDescription["width"])){
			$this->width = (int) trim($moduleDescription["width"]);
		}
		
		if(isset($moduleDescription["style"]) && strlen($moduleDescription["style"]) > 0){
			$this->style = preg_split("/[,]/", trim($moduleDescription["style"]));
		}
	}
	
	public function toJson($withContent = false){
		$moduleDescription = [];
		$moduleDescription["id"] = $this->id;
		$moduleDescription["width"] = $this->width;
		$moduleDescription["style"] = implode(",", $this->style);
		
		if($withContent){
			$description = new GridPageModuleDescription($this);			
			$moduleDescription["description"] = $description->toArray();
		}
		
		return $moduleDescription;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function setWidth($width) {
		$this->width = $width;
	}
	
	public function getStyle() {
		return $this->style;
	}
	
	public function setStyle($style) {
		$this->style = $style;
	}
	
	/**
	 * @return GridPage
	 */
	public function getPage() {
		return $this->page;
	}
	
	public function setPage($page) {
		$this->page = $page;
	}
	
	/**
	 * @return GridPageModuleDescription
	 */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
}
