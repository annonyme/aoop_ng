<?php
/*
 * Copyright (c) 2015/2016/2017 Hannes Pries <http://www.annonyme.de>
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace PDBC;

use ReflectionClass;

class PDBCObjectMapper{
	private static $cache=[];
	private static $fullCache=[];
	
	public static $ORDER_ASC = "ASC";
	public static $ORDER_DESC = "DESC";
	 
	public function __contruct(){
		 
	}
	
	private function analyzeClass($ref){
		if(isset(self::$fullCache[$ref->getName()])){
			return self::$fullCache[$ref->getName()];
		}
		else{
			$cla=array(
				"name" => $ref->getName(),
				"table" => $this->getTableName($ref),
				"reflection" => $ref,	
			);
			
			$props=$ref->getProperties();
			$map=[];
			foreach($props as $prop){
				$prop->setAccessible(true);
				$colName=$this->getColumnName($prop);
				$primary=$this->isPrimary($prop);
				$map[$colName]=[
						"name" => $prop->getName(),
						"reflection" => $prop,
						"columnName" => $colName,
						"type" => $this->getColumnType($prop),
						"primary" => $primary,
				];
				
				if($primary){
					$cla["primary"]=$map[$colName];
				}
			}
			$cla["properties"]=$map;
			
			self::$fullCache[$ref->getName()]=$cla;
			return $cla;
		}
	}
	 
	private function loadProperties($ref){
		if(isset(self::$cache[$ref->getName()])){
			return self::$cache[$ref->getName()];
		}
		else{
			$props=$ref->getProperties();
			$map=[];
			foreach($props as $prop){
				$prop->setAccessible(true);
				$map[$this->getColumnName($prop)]=$prop;
			}
			self::$cache[$ref->getName()]=$map;
			return self::$cache[$ref->getName()];
		}
	}
	 
	private function getColumnName($prop){
		$result=null;
		$doc=$prop->getDocComment();
		if(preg_match("/@dbcolumn=[a-zA-Z0-9_]+/",$doc)){
			$result=preg_replace("/^.+@dbcolumn=([a-zA-Z0-9_]+)\s.+$/Uis","$1",trim($doc));
		}
		return $result;
	}
	
	private function getColumnType($prop){
		$result=null;
		$doc=$prop->getDocComment();
		if(preg_match("/@dbtype=[a-z]+/",$doc)){
			$result=preg_replace("/^.+@dbtype=([a-z]+)\s.+$/Uis","$1",trim($doc));
		}
		return $result;
	}
	
	private function isPrimary($prop){
		$doc=$prop->getDocComment();
		return preg_match("/@dbprimary/",$doc);
	}
	
	private function getTableName($clazz){
		$result=null;
		$doc=$clazz->getDocComment();
		if(preg_match("/@dbtable=[a-zA-Z0-9_]+/",$doc)){
			$result=preg_replace("/^.+@dbtable=([a-zA-Z0-9_]+)\s.+$/Uis","$1",trim($doc));
		}
		return $result;
	}
	 
	private function fillObject($row, $ref){
		$obj=$ref->newInstance();
		$props=$this->loadProperties($ref);
		foreach($row as $key => $value){
			if(isset($props[$key])){
				$props[$key]->setValue($obj,$value);
			}
		}
		return $obj;
	}
	 
	public function queryList($db, $sql, $className){
		if(is_object($className)){
			$className=get_class($className);
		}
		
		$db->executeQuery($sql);
		$ref=new ReflectionClass($className);
		$result=[];
		for($i=0;$i<$db->getCount();$i++){
			$result[count($result)]=$this->fillObject($db->getRow($i),$ref);
		}
		return $result;		 
	}
	 
	public function querySingle($db, $sql, $className){
		$db->executeQuery($sql);
		$ref=new ReflectionClass($className);
		$result=null;
		for($i=0;$i<$db->getCount();$i++){
			$result=$this->fillObject($db->getRow($i),$ref);
		}
		if($result==null){
			$result=$ref->newInstance();
		}
		return $result;
	}
	
	public function load($db,$primaryValue, $className){
		$ref=new ReflectionClass($className);
		$cla=$this->analyzeClass($ref);
		$sql="SELECT * FROM ".$cla["table"]." WHERE ".$cla["primary"]["columnName"]."=#{primaryValue}";
		$stmt=new PDBCSQLStatement($sql);
		$stmt->set("primaryValue", $primaryValue, $cla["primary"]["type"]);
		return $this->querySingle($db, $stmt->getSQL(), $className);
	}

    /**
     * @param $db
     * @param $column
     * @param $value
     * @param string $type
     * @param $className
     * @param null $orderby
     * @param string $orderbytype
     * @return array
     * @throws \ReflectionException
     */
	public function loadListByColumn($db, $column, $value, $type="int", $className, $orderby=null, $orderbytype="ASC"){
		$ref=new ReflectionClass($className);
		$cla=$this->analyzeClass($ref);
		$sql="SELECT * FROM ".$cla["table"]." WHERE ".$column."=#{value}";
		if($orderby){
			$sql.=" ORDER BY ".$orderby." ".$orderbytype;
		}
		$stmt=new PDBCSQLStatement($sql);
		$stmt->set("value", $value, $type);
		return $this->queryList($db, $stmt->getSQL(), $className);
	}
	
	public function loadList($db, $className, $orderby=null, $orderbytype="ASC"){
		$ref=new ReflectionClass($className);
		$cla=$this->analyzeClass($ref);
		$sql="SELECT * FROM ".$cla["table"]." ";
		if($orderby){
			$sql.=" ORDER BY ".$orderby." ".$orderbytype;
		}
		$stmt=new PDBCSQLStatement($sql);
		return $this->queryList($db, $stmt->getSQL(), $className);
	}
	
	public function delete($db, $entity){
		$ref=new ReflectionClass(get_class($entity));
		$cla=$this->analyzeClass($ref);
		
		$id=null;
		if(isset($cla["primary"])){
			$id=$cla["primary"]["reflection"]->getValue($entity);
		}
		
		if($id){
			$sql="DELETE FROM ".$cla["table"]." WHERE ".$cla["primary"]["columnName"]."=#{primaryValue}";
			$stmt=new PDBCSQLStatement($sql);
			$stmt->set("primaryValue", $cla["primary"]["reflection"]->getValue($entity), $cla["primary"]["type"]);
			$db->execute($stmt->getSQL());
		}
	}
	
	public function merge($db, $entity){
		$ref=new ReflectionClass(get_class($entity));
		$cla=$this->analyzeClass($ref);
		
		$id=null;
		if(isset($cla["primary"])){
			$id=$cla["primary"]["reflection"]->getValue($entity);
		}
		
		$sql="";
		$isInsert=false;
		if($id){
			$sql="UPDATE ".$cla["table"]." SET ";
			$sqlValue="";
			foreach($cla["properties"] as $p){
				if(!$p["primary"]){
					if(strlen($sqlValue)>0){
						$sqlValue.=",";
					}
					$sqlValue.="".$p["columnName"]."=#{".$p["columnName"]."} ";
				}				
			}
			$sql.=$sqlValue." WHERE ".$cla["primary"]["columnName"]."=#{".$cla["primary"]["columnName"]."}";
		}
		else{
			$sql="INSERT INTO ".$cla["table"]." (";
			$cols="";
			$values="";
			foreach($cla["properties"] as $p){
				if(!$p["primary"]){
					if(strlen($values)>0){
						$cols.=",";
						$values.=",";
					}
					$cols.=" ".$p["columnName"]." ";
					$values.=" #{".$p["columnName"]."} ";
				}				
			}
			$sql.=$cols.")VALUES(".$values.")";
			$isInsert=true;
		}
		if(strlen($sql)>0){
			$stmt=new PDBCSQLStatement($sql);
			foreach($cla["properties"] as $p){
				$stmt->set($p["columnName"],$p["reflection"]->getValue($entity),$p["type"]);
			}
			//echo $stmt->getSQL();
			$result=$db->execute($stmt->getSQL());
			if($isInsert && !is_bool($result)){
				$cla["primary"]["reflection"]->setValue($entity, $result);
			}			
		}
		
		return $entity;
	}
}