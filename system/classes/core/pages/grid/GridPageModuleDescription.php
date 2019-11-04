<?php
namespace core\pages\grid;

class GridPageModuleDescription{

	private $reference = null;
	private $path = "";
	private $content = "";
	private $style = [];
	private $id = "";
	
	/**
	 * @var GridPageModuleRestriction[]
	 */
	private $restrictions = [];
	
	private $rendererClass = "";
	
	private $scope = "global";
	
	/**
	 * @var GridPageModule
	 */
	private $renderer = null;
	
	/**
	 * @param GridPageModuleReference|nul $reference
	 */
	public function __construct($reference = null){
		if($reference){
			$pagePath = $reference->getPage()->getFolder();
			if(!preg_match("/\/$/", $pagePath)){
				$pagePath .= "/";
			}
			$this->reference = $reference;
			$this->load($pagePath."pagemodules/".$reference->getId().".grid.module.json");
		}		
	}
	
	public function isValid(){
		$result = true;
		foreach($this->restrictions as $rest){
			if(!$rest->validate()){
				$result = false;
			}
		}
		return $result;
	}
	
	public function render(){
		$result = "";
		try{
			if($this->renderer){
				$result = $this->renderer->render($this);
			}
		}
		catch(\Exception $e){
			
		}
		return $result;
	}
	
	public function loadByJson($json, $reference = null){
		if($reference){
			$this->reference = $reference;
			$this->id = $this->reference->getId();
			
			$path = $reference->getPage()->getFolder();
			$filename = $path . "pagemodules/" . $this->id .".grid.module.json";
			
			$this->path = $filename;
		}
		
		if(isset($json["content"])){
			$this->content = trim($json["content"]);
		}
			
		if(isset($json["scope"])){
			$this->scope = trim($json["scope"]);
		}
			
		if(isset($json["className"])){
			$this->rendererClass = trim($json["className"]);
			if(preg_match("/[_]/", $this->rendererClass)){
				$this->rendererClass = preg_replace("/[_]/", "\\", $this->rendererClass);
			}
			try{
				$clazz = new \ReflectionClass($this->rendererClass);
				$this->renderer = $clazz->newInstance();
			}
			catch(\Exception $eRen){
					
			}
		}
			
		if(isset($json["style"]) && strlen($json["style"]) > 0){
			$this->style = preg_split("/[,]/", trim($json["style"]));
		}
		
		if(isset($json["restrictions"])){
			foreach ($json["restrictions"] as $rest){
				$this->restrictions[] = new GridPageModuleRestriction($rest);
			}
		}
	}
	
	public function load($path){
		if(is_file($path)){
			$this->path = $path;
			$json = json_decode(file_get_contents($path), true);
			
			$fileParts = preg_split("/\//", $path);
			$id = preg_replace("/\.grid\.module\.json$/", "", $fileParts[count($fileParts)-1]);
			$this->id = $id;
			
			$this->loadByJson($json);
		}
	}
	
	/**
	 * @param string|null $id
	 * @param array|null $json
	 * @param GridPageModuleReference|null $reference
	 */
	public function save($json = null, $id = null, $reference = null){
		if($json && is_array($json) && $id !== null && $reference !== null){
			$path = $reference->getPage()->getFolder();
			$filename = $path . "pagemodules/" . $id .".grid.module.json";
			file_put_contents($filename, json_encode($json));
		}
		else if($json === null) {
			$this->save($this->toArray(), $this->id, $this->reference);
		}
	}
	
	public function toArray(){
		$data = [];
		$data["id"] = $this->id;
		$data["scope"] = $this->scope;
		$data["content"] = $this->content;
		$data["className"] = preg_replace("/[^a-zA-Z0-9]/", "_", $this->rendererClass);
		$data["style"] = implode(",", $this->style);
		$data["restrictions"] = [];
		foreach($this->restrictions as $rest){
			$data["restrictions"][] = $rest->getRawContent();
		}
		return $data;
	}
	
	public function toJson(){
		return json_encode($this->toArray());
	}
	
	public function delete(){
	
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getStyle() {
		return $this->style;
	}
	
	public function setStyle($style) {
		$this->style = $style;
	}
	
	public function getRestrictions() {
		return $this->restrictions;
	}
	
	public function setRestrictions($restrictions) {
		$this->restrictions = $restrictions;
	}
	
	public function getRenderer() {
		return $this->renderer;
	}
	
	public function setRenderer($renderer) {
		$this->renderer = $renderer;
	}
	
	/**
	 * @return GridPageModuleReference
	 */
	public function getReference() {
		return $this->reference;
	}
	
	public function setReference($reference) {
		$this->reference = $reference;
	}
	
	public function getScope() {
		return $this->scope;
	}
	
	public function setScope($scope) {
		$this->scope = $scope;
	}
	
	public function getRendererClass() {
		return $this->rendererClass;
	}
	
	public function setRendererClass($rendererClass) {
		$this->rendererClass = $rendererClass;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
}