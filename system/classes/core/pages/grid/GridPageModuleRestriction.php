<?php
namespace core\pages\grid;

class GridPageModuleRestriction{
	private $content = "";
	
	public function __construct($content){
		$this->content = $content;
	}
	
	public function validate(){
		$result = true;
		if(isset($this->content["type"])){
			if($this->content["type"] == "daterange"){
				$currentTime = time();
				if($this->content["from"] <= $currentTime && $this->content["to"] >= $currentTime){
					$result = true;
				}
				else{
					$result = false;
				}
			}
			else if ($this->content["type"] == "session_exists"){
				$result = isset($_SESSION[$this->content["key"]]) && $_SESSION[$this->content["key"]]!== null;
			}
		}
		return $result;
	}
	
	public function getRawContent(){
		return $this->content;
	}
	
}