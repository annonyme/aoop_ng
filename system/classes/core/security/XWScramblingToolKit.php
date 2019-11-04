<?php
/*
 * Created on 17.10.2011
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

namespace core\security;

use core\utils\XWArrayList;
 
class XWScramblingToolKit{
	
	private $prefix="#_#_";
	
	public function __construct(){
		
	}
	
	public function simpleScrambling($text){
		$result="";
		 
		$firstPart=new XWArrayList();
		$secondPart=new XWArrayList();
		
		for($i=0;$i<strlen($text);$i++){
			if($i%2==0){
				$firstPart->add(substr($text,$i,1));
			}
			else{
				$secondPart->add(substr($text,$i,1));
			}
		} 
		
		$result=$this->prefix;
		for($i=0;$i<$firstPart->size();$i++){
			$result.=$firstPart->get($i);
		}
		for($i=0;$i<$secondPart->size();$i++){
			$result.=$secondPart->get($i);
		}
		
		return $result;
	}
	
	public function simpleDescrambling($text){
		$result="";
		
		if(preg_match("/^".$this->prefix."/",$text)){
			$text=preg_replace("/^".$this->prefix."/","",$text);
			
			$lastFirstPartIndex=0;
			if(strlen($text)%2==0){
				$lastFirstPartIndex=strlen($text)/2;
			}
			else{
				$lastFirstPartIndex=(strlen($text)+1)/2;
			}
			
			for($i=0;$i<$lastFirstPartIndex;$i++){
				$result.=substr($text,$i,1);
				
				if(($i+$lastFirstPartIndex)<strlen($text)){
					$result.=substr($text,$i+$lastFirstPartIndex,1);
				}
			}
		} 
		else{
			$result=$text;
		}
		 
		return $result;
	}
	
	public function simpleScramblingWithRounds($text,$rounds=1){
		for($i=0;$i<$rounds;$i++){
			$text=$this->simpleScrambling($text);
		}
		return $text;
	}
	
	public function simpleDescramblingWithRounds($text,$rounds=1){
		for($i=0;$i<$rounds;$i++){
			$text=$this->simpleDescrambling($text);
		}
		return $text;
	}
} 
?>