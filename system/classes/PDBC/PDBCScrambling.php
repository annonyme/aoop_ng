<?php

 /*
  * Copyright (c) 2014 Hannes Pries <http://www.annonyme.de>
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

class PDBCScrambling{
	
	private $prefix="#_#_";
	
	public function __construct(){
		
	}
	
	public function simpleScrambling($text){
		$result="";
		 
		$firstPart=[];
		$secondPart=[];
		
		for($i=0;$i<strlen($text);$i++){
			if($i%2==0){
				$firstPart[count($firstPart)]=substr($text,$i,1);
			}
			else{
				$secondPart[count($secondPart)]=substr($text,$i,1);
			}
		} 
		
		$result=$this->prefix;
		for($i=0;$i<count($firstPart);$i++){
			$result.=$firstPart[$i];
		}
		for($i=0;$i<count($secondPart);$i++){
			$result.=$secondPart[$i];
		}
		
		return $result;
	}
	
	public function simpleDescrambling($text){
		$result="";
		/*
		 * String result="";
		if(scrambledString.indexOf(PREFIX)==0){
			scrambledString=scrambledString.replace(PREFIX, "");
			int lastFirstPartIndex=0;
			if(scrambledString.length()%2==0){
				lastFirstPartIndex=scrambledString.length()/2;
			}
			else{
				lastFirstPartIndex=(scrambledString.length()+1)/2;
			}
			
			for(int i=0;i<lastFirstPartIndex;i++){
				result+=scrambledString.substring(i,i+1);
				
				if((i+lastFirstPartIndex)<scrambledString.length()){
					result+=scrambledString.substring((i+lastFirstPartIndex), (i+lastFirstPartIndex+1));
				}
			}
		}
		else{
			result=scrambledString;
		}
		return result;
		 */
		
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
}