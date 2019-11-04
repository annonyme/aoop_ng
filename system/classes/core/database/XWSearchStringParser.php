<?php

namespace core\database;

/*
 * Created on 14.07.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2011 Hannes Pries <http://www.annonyme.de>
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
 
class XWSearchStringParser{
	
	public function __construct(){
		
	}
	
	/**
	 * whitespace = OR, + = AND, | = OR, (....) = (.....)
	 * converts a string from an input field to the specific sql expression
	 * for the where-part of the sql-statement
	 */
	public function simpleIneffectiveParsing($searchString,$columnName){
		$search=trim(preg_replace("/([^a-zA-z0-9])/Uis"," $1 ",$searchString));
		$search=preg_replace("/\s+/","  ",$search);
		$search=preg_replace("/(^|\s)(\w+)(\s|$)/Uis"," _col_ like '% $2 %' ",$search);		
		$search=preg_replace("/\s{2}/Uis"," OR ",$search);		
		$search=preg_replace("/OR\s+\+\s+OR/Uis"," AND ",$search);		
		$search=preg_replace("/OR\s+\|\s+OR/Uis"," OR ",$search);		
		$search=preg_replace("/\(\s+OR/Uis","(",$search);		
		$search=preg_replace("/OR\s+\)/Uis",")",$search);		
		$search=preg_replace("/\s+/"," ",$search);		
		$search=preg_replace("/_col_/Uis",$columnName,$search);
		return trim($search);
	}
	
	public function simpleStringCleaning($text){
		$text=preg_replace("/[^a-zA-Z0-9_\s]/Uis"," ",$text);
		$text=preg_replace("/\s+/is"," ",$text);
		return trim($text);
	}	
	
	public function checkUserName($userName){
		return $this->userNameCleaning($userName)===$userName || filter_var($userName, FILTER_VALIDATE_EMAIL);
	}
	
	public function userNameCleaning($userName){
		$text=preg_replace("/[^a-zA-Z0-9_\-]/is"," ",$userName);
		$text=preg_replace("/\s+/is"," ",$text);
		return trim($text);
	}
}
