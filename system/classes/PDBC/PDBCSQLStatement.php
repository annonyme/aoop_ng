<?php
namespace PDBC;

if(!function_exists("boolval")){
	function boolval($value){
		return (bool) $value;
	}
}

class PDBCSQLStatement{
	private $sql="";
	private $workingCopy="";
	private $sec=null;
	
	public function __construct($sql){
		$this->sql=$sql;
		$this->workingCopy="".$sql;
		$this->sec=new PDBCSQLSecure();
	}
	
	public function newSQL($sql){
		$this->sql=$sql;
		$this->workingCopy="".$sql;
		return $this;
	}
	
	public function getSQL(){
		return $this->workingCopy;
	}
	
	public function getTemplateSQL(){
		return $this->sql;
	}
	
	public function setString($name,$value){
		$sec=$this->sec;
		$value=$sec->replaceSingleQuotesByHTML($value);
		$value=$sec->replaceDoubleQuotesByHTML($value);
		$value=$sec->replaceEqualsByHTML($value);
		$value=$sec->replaceWildcardsByHTML($value);
		$value=$sec->replaceEscapesByHTML($value);
	
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","'".trim($value)."'",$this->workingCopy);
		return $this;
	}
	
	public function setStringWithWildcards($name,$value,$start,$end){
		$sec=$this->sec;
		$value=$sec->replaceSingleQuotesByHTML($value);
		$value=$sec->replaceDoubleQuotesByHTML($value);
		$value=$sec->replaceEqualsByHTML($value);
		$value=$sec->replaceWildcardsByHTML($value);
		$value=$sec->replaceEscapesByHTML($value);
	
		$beginValue="";
		if($start){
			$beginValue="%";
		}
	
		$endValue="";
		if($end){
			$endValue="%";
		}
	
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","'".$beginValue.trim($value).$endValue."'",$this->workingCopy);
		return $this;
	}
	
	public function setInt($name,$value){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","".intval($value),$this->workingCopy);
		return $this;
	}
	
	public function setIntAbs($name,$value){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","".abs(intval($value)),$this->workingCopy);
		return $this;
	}
	
	/**
	 * true=1, false=0
	 */
	public function setBooleanAsInt($name,$value){
		$intValue=0;
		if(boolval($value)){
			$intValue=1;
		}
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","".$intValue,$this->workingCopy);
		return $this;
	}
	
	public function setNumber($name,$value){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","".floatval($value),$this->workingCopy);
		return $this;
	}
	
	public function setDouble($name,$value){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","".doubleval($value),$this->workingCopy);
		return $this;
	}
	
	public function setDate($name, $date =null){
	    if($date == ''){
	        $date = "null";
	    }
	    else{
	        $date = "'".$date."'";
	    }
	    $this->workingCopy=preg_replace("/#\{".$name."\}/i", $date, $this->workingCopy);
	    return $this;
	}
	
	public function setCurrentDate($name){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","CURRENT_DATE",$this->workingCopy);
		return $this;
	}
	
	public function setCurrentDateTime($name){
		$this->workingCopy=preg_replace("/#\{".$name."\}/i","CURRENT_TIMESTAMP",$this->workingCopy);
		return $this;
	}
	
	public function clear(){
		$this->workingCopy="".$this->sql;
	}
	
	public function set($name,$value,$type="string"){
		switch ($type) {
			case "string":
				$this->setString($name, $value);
				break;
			case "int":
				$this->setInt($name, $value);
				break;
			case "boolean":
				$this->setBooleanAsInt($name, $value);
				break;
			case "bool":
				$this->setBooleanAsInt($name, $value);
				break;
			case "number":
				$this->setNumber($name, $value);
				break;
			case "float":
				$this->setNumber($name, $value);
				break;
			case "double":
				$this->setDouble($name, $value);
				break;
			case "intabs":
				$this->setIntAbs($name, $value);
				break;
			case "date":
			    $this->setDate($name, $value);
			    break;
		}
	}

    /**
     * @return string
     */
    public function getWorkingCopy(): string
    {
        return $this->workingCopy;
    }

    /**
     * @param string $workingCopy
     */
    public function setWorkingCopy(string $workingCopy)
    {
        $this->workingCopy = $workingCopy;
    }
}