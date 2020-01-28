<?php
namespace core\database;

use ReflectionClass;
use Exception;

class XWDataMapper{
	public function __construct(){
		
	}
	
	private function getColumnNameFromDoc($doc){
		$result=null;
		if(preg_match("/@dbcolumn=[a-zA-Z0-9_]+/",$doc)){
			$result=preg_replace("/^.+@dbcolumn=([a-zA-Z0-9_]+)\s.+$/Uis","$1",trim($doc));
		}
		return $result;
	}

    /**
     * @param $obj
     * @param $array
     *
     * @return mixed
     * @throws Exception
     */
	public function mapData($obj,$array){
		try{
			$ref=new ReflectionClass(get_class($obj));
			$props=$ref->getProperties();
			$cnt=count($props);
			for($i=0;$i<$cnt;$i++){
				$prop=$props[$i];
				$prop->setAccessible(true);
				$column=$this->getColumnNameFromDoc($prop->getDocComment());
				if($column!=null){
					if(isset($array[$column])){
						$prop->setValue($obj,$array[$column]);
					}
				}
			}
		}
		catch(Exception $e){
			throw $e;
		}
		return $obj;
	}
}
