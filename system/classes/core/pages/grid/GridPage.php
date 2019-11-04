<?php
namespace core\pages\grid;

use core\pages\PageInterface;

class GridPage implements PageInterface{
	private $path="";
	private $folder="";
	private $parentAlias="";
	private $date="";
	private $userId=0;
	private $changefreq="daily";
	private $hidden = false;
	
	private $keywords = "";
	private $metadescription = "";
	private $alias = "";
	private $title = "";
	
	/**
	 * @var GridPageModuleReference[]
	 */
	private $modules = [];	
	
	//TODO subgrids with modules in them
	
	private $names=[];
	private $intlAlias = [];
	
	public function readName($locale){
		$name="";
		if(isset($this->names[$locale])){
			$name=$this->names[$locale];
		}
		else if(isset($this->names["default"])){
			$name=$this->names["default"];
		}
		else{
			$name=$this->alias;
		}
		return $name;
	}
	
	public function loadByJson($folder, $json){
		$this->json=$json;
		
		if(!preg_match("/\/$/i",$folder)){
			$folder=$folder."/";
		}
		$this->folder=$folder;
		
		$this->intlAlias["default"] = $this->alias;
		
		if(isset($json["intlaliases"])){
			foreach ($json["intlaliases"] as $intl){
				$this->intlAlias[$intl["locale"]] = $intl["alias"];
			}
		}
		
		if(isset($json["hidden"])){
			$this->hidden=strtolower($json["hidden"])=="true";
		}
		if(isset($json["name"])){
			$this->names["default"]=$json["name"];
		}
		if(isset($json["names"])){
			$names=$json["names"];
			$namesCount=count($names);
			for($i=0;$i<$namesCount;$i++){
				$name=$names[$i];
				$this->names[$name["locale"]]=$name["value"];
			}
		}
		if(isset($json["parent"])){
			$this->parentAlias=$json["parent"];
		}
		if(isset($json["userid"])){
			$this->userId=intval($json["userid"]);
		}
		if(isset($json["changefreq"])){
			$this->changefreq=$json["changefreq"];
		}
		if(isset($json["date"])){
			$this->date=$json["date"];
		}
			
		if(isset($json["alias"])){
			$this->alias=trim($json["alias"]);			
			$this->path = $this->folder.$this->alias.".grid.page.json";			
		}
		else{
			throw new \Exception("alias not found");
		}
			
		if(isset($json["title"])){
			$this->title=trim($json["title"]);
		}
			
		if(isset($json["keywords"])){
			$this->keywords=$json["keywords"];
		}
			
		if(isset($json["metadescription"])){
			$this->metadescription=$json["metadescription"];
		}
			
		$this->modules = [];
		if(isset($json["moduleReferences"])){			
			foreach ($json["moduleReferences"] as $module){
				$this->modules[] = new GridPageModuleReference($module, $this);
			}
		}
	}
	
	public function load($folder, $callName){
		if(!preg_match("/\/$/i",$folder)){
			$folder=folder."/";
		}
		$path=$folder.$callName.".grid.page.json";
		if(file_exists($path)){
			$this->path=$path;
			$this->folder=$folder;
			$this->alias=$callName;
			$this->intlAlias["default"] = $this->alias;
				
			$json=json_decode(file_get_contents($path),true);
			$this->loadByJson($folder, $json);
		}	
	}
	
	public function save($data = null, $folder = null, $callName = null){
		$result = false;
		if($data && is_array($data) && $folder && is_dir($folder) && $callName && strlen($callName) > 0){
			$filename = $folder . $callName .".grid.page.json";
			file_put_contents($filename, json_encode($data));
			return true;
		}
		else if($data === null){			
			foreach ($this->modules as $module){
				$module->getDescription()->save();
			}
			$result = $this->save($this->toArray(), $this->folder, $this->alias);
		}
		return $result;
	}
	
	public function toArray($withContent = false){
		$json = [];
		$json["hidden"] = $this->hidden ? "true" : "false";
		$json["name"] = $this->names["default"];
		$json["parent"] = $this->parentAlias;
		$json["changefreq"] = $this->changefreq;
		$json["keywords"] = $this->keywords;
		$json["metadescription"] = $this->metadescription;
		$json["alias"] = $this->alias;
		$json["title"] = $this->title;
		$json["moduleReferences"] = [];
		foreach ($this->modules as $mod){
			$json["moduleReferences"][] = $mod->toJson($withContent);
		}
		$json["names"] = [];
		foreach($this->names as $locale => $value){
			if($locale != "default"){
				$item = ["locale" => $locale, "name" => $value];
				$json["names"][] = $item;
			}
		}
		
		return $json;
	}
	
	public function toJson(){
		return json_encode($this->toArray());
	}
	
	public function toEditJson(){
		return json_encode($this->toArray(true));
	}
	
	public function delete(){
		
	}
	
	/**
	 * @deprecated
	 */
	public function getCallName() {
		return $this->alias;
	}
	
	/**
	 * @deprecated
	 * @param string $callName
	 */
	public function setCallName($callName) {
		$this->alias = $callName;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	public function getFolder() {
		return $this->folder;
	}
	
	public function setFolder($folder) {
		$this->folder = $folder;
	}
	
	/**
	 * @deprecated
	 */
	public function getParentPageCallName() {
		return $this->parentAlias;
	}
	
	/**
	 * @deprecated
	 * @param string $parentPageCallName
	 */
	public function setParentPageCallName($parentPageCallName) {
		$this->parentAlias = $parentPageCallName;
	}
	
	public function getDate() {
		return $this->date;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}
	
	public function getUserId() {
		return $this->userId;
	}
	
	public function setUserId($userId) {
		$this->userId = $userId;
	}
	
	public function getChangefreq() {
		return $this->changefreq;
	}
	
	public function setChangefreq($changefreq) {
		$this->changefreq = $changefreq;
	}
	
	/**
	 * @return GridPageModuleDescription[]
	 */
	public function getModules() {
		return $this->modules;
	}
	
	public function setModules($modules) {
		$this->modules = $modules;
	}
	
	public function getNames() {
		return $this->names;
	}
	
	public function setNames($names) {
		$this->names = $names;
	}
	
	public function isHidden() {
		return $this->hidden;
	}
	
	public function setHidden($hidden) {
		$this->hidden = $hidden;
		return $this;
	}
	
	public function getKeywords() {
		return $this->keywords;
	}
	
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	public function getMetadescription() {
		return $this->metadescription;
	}
	
	public function setMetadescription($metadescription) {
		$this->metadescription = $metadescription;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getParentAlias() {
		return $this->parentAlias;
	}
	
	public function setParentAlias($parentAlias) {
		$this->parentAlias = $parentAlias;
	}
	/**
	 * {@inheritDoc}
	 * @see \core\pages\PageInterface::getName()
	 */
	public function getName() {
		return $this->names["default"];
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \core\pages\PageInterface::getIntlAlias()
	 */
	public function getIntlAlias($locale = "default") {
		if(!isset($this->intlAlias[$locale])){
			$locale = "default";
		}
		return $this->intlAlias[$locale];

	}

	/**
	 * {@inheritDoc}
	 * @see \core\pages\PageInterface::hasIntlAlias()
	 */
	public function hasIntlAlias($alias) {
		$result = false;
		try{
			foreach($this->intlAlias as $intlAlias){
				if($intlAlias == $alias){
					$result = true;
				}
			}
		}
		catch(\Exception $e){
			
		}
		return $result;
	}

}
