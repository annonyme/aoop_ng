<?php

 /*
  * Copyright (c) 2014/2015 Hannes Pries <http://www.annonyme.de>
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


use DOMDocument;

class PDBCDataSource{
	
	private $host="";
	private $port="";
	private $sid="";
	private $userName="";
	private $userPassword="";
	
	private $name="";
	private $type="";
	private $className=""; 
	
	/**
	 * Liest die Daten für eine Datenbank-Connection aus der Datei
	 * datasources.xml aus und stellt diese der DBFatory zur Verfügung
	 */
	public function __construct($dsName,$file){
		$this->name=$dsName;
		
		$doc = new DOMDocument();
		$doc->load($file);
		$sources=$doc->getElementsByTagName("datasource");
		foreach($sources as $source){
            $attrs=$source->attributes;
            foreach($attrs as $attr){
                if($attr->value==$dsName && $attr->name=="name"){
                	$nodes=$source->childNodes;
                	$node=null;
                	$tk=new PDBCScrambling();
                	foreach ($nodes as $node){
                		
                		if($node->nodeName=="host"){
							if($node->nodeValue == '_env_'){
								$this->host=getenv('db_host');
							}
							else {
								$this->host=$tk->simpleDescrambling($node->nodeValue);
							}							
                		}
                		if($node->nodeName=="port"){
							if($node->nodeValue == '_env_'){
								$this->port=getenv('db_port');
							}
							else {
								$this->port=$node->nodeValue;
							}
                		}
                		if($node->nodeName=="sid"){
							if($node->nodeValue == '_env_'){
								$this->sid=getenv('db_sid');
							}
							else {
								$this->sid=$tk->simpleDescrambling($node->nodeValue);
							}							
                		}
                		if($node->nodeName=="username"){
							if($node->nodeValue == '_env_'){
								$this->userName=getenv('db_username');
							}
							else {
								$this->userName=$tk->simpleDescrambling($node->nodeValue);
							}
							
                		}
                		if($node->nodeName=="userpassword"){                			
							if($node->nodeValue == '_env_'){
								$this->userPassword=getenv('db_userpassword');
							}
							else {
								$this->userPassword=$tk->simpleDescrambling($node->nodeValue);
							}
                		}
                	}
                	foreach($attrs as $innerAttr){
                		if($innerAttr->name=="class"){
                			$this->className=$innerAttr->value;
                		}
                		//for PDO
                		if($innerAttr->name=="type"){
                			$this->type=$innerAttr->value;
                			if($this->className==""){
                				$this->className=$innerAttr->value;
                			}
                		}
                	}
                	
            	}
            }            
       }	
	}
	
	/**
	 * gibt die Verbindungsdaten in der Form einer JDBC-URL zurück.
	 * @return string
	 */
	public function getJDBCURL(){
		return "jdbc:".$this->type.":thin:".$this->host.":".$this->port.":".$this->sid;
	}
	
	/**
	 * gibt die Verbindungsdaten in der Form einer PDO-URL zurück.
	 * @return string
	 */
	public function getPDOURL(){
		return $this->type."://".$this->userName.":".$this->userPassword."@".$this->host."/".$this->sid;
	}
	
	/**
	 * Gibt den Typ der Datenbank zurück. z.B. "oracle"
	 * @return string
	 */
	public function getDataBaseType(){
		return $this->type;
	}
	
	/**
	 * Gibt den Klassennamen für die Datenbankverbindung zurück
	 * @return string
	 */
	public function getClassName(){
		return $this->className;
	}
	
	/**
	 * Benutzername für die Datenbankverbindung.
	 * @return string
	 */
	public function getUserName(){
		return $this->userName;
	}
	
	/**
	 * Benutzerpasswort für die Datenbankverbindung.
	 * @return string
	 */
	public function getUserPassword(){
		return $this->userPassword;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
	    $this->name=$name;	
	}
}