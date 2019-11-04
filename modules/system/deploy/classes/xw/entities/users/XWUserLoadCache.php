<?php
/*
 * Created on 19.01.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace xw\entities\users;

class XWUserLoadCache{
	
	private $cache=[];
	private static $instance=null;
	
	public function __construct(){
		$this->cache=[];
	}
	
	/**
	 * @return XWUserLoadCache
	 */
	public static function instance(){
		if(self::$instance==null){
			self::$instance=new XWUserLoadCache();
		}
		return self::$instance;
	}
	
	/**
	 * @return XWUser
	 * @param int $id
	 */
	public function loadUser($id){
		if(isset($this->cache[$id])){
			return $this->cache[$id];
		}
		else{
			$user=new XWUser();
			$user->load($id);
			$this->cache[$user->getId()]=$user;
			
			return $this->cache[$id];
		}
	}
}
