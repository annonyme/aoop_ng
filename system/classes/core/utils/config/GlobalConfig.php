<?php
namespace core\utils\config;

class GlobalConfig{
	private static $instance=null;
	private $values=[];
	
	private function __construct($file){
		if(is_file($file)){
			$this->values = json_decode(file_get_contents($file), true);
		}
	}
	
	/**
	 * @return GlobalConfig
	 * @param string|null $file
	 */
	public static function instance($file=null){
		if(self::$instance==null){
			self::$instance=new GlobalConfig($file);
		}
		return self::$instance;
	}
	
	public function setValue($key, $value){
		$this->values[$key]=$value;
	}
	
	public function getValue($key, $default=""){
		$result=$default;
		if(isset($this->values[$key])){
			$result=$this->values[$key];
		}
		return $result;
	}
	
	public function getEnv(){
	    return getenv('AOOP_ENV') ?: 'default';
	}
	
	public function isDevMode(){
	    return $this->getEnv() === "dev";
	}
}