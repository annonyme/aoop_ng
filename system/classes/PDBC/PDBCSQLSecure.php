<?php 
 /*
  * Copyright (c) 2008 Hannes Pries <http://www.annonyme.de>
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
 
class PDBCSQLSecure{
	public function __construct(){
		
	}
	
	public function removeSemicolonsFromNonStringParts($sql,$replace=" xx "){
		$sql=preg_replace("/__semicol__/","",$sql);
		$sql=preg_replace_callback("/\'(.*)\'/Uis",create_function('$a','return "\'".preg_replace("/;/","__semicol__",$a[1])."\'";'),$sql);
        $sql=preg_replace("/;/",$replace,$sql);
        $sql=preg_replace("/__semicol__/",";",$sql);
        
        $sql=preg_replace("/__comment__/","",$sql);
        $sql=preg_replace_callback("/\'(.*)\'/Uis",create_function('$a','return "\'".preg_replace("/\-\-+/","__comment__",$a[1])."\'";'),$sql);
        $sql=preg_replace("/\-\-+/",$replace,$sql);
        $sql=preg_replace("/__comment__/","--",$sql);
        return $sql;
	}
	
	public function removeSingleQuotes($text){
		return preg_replace("/\'/","",$text);
	}
	
	public function replaceSingleQuotesByHTML($text){
		return preg_replace("/(\\\)?\'/","&#39;",$text);
	}
	
	public function replaceDoubleQuotesByHTML($text){
		return preg_replace("/(\\\)?\"/","&#34;",$text);
	}
	
	public function replaceEscapesByHTML($text){
		return preg_replace("/(\\\)/","&#92;",$text);
	}
	
	public function replaceSemicolonsByHTML($text){
		return preg_replace("/;/","&#59;",$text);
	}
	
	public function replaceWildcardsByHTML($text){
		return preg_replace("/%/","&#37;",$text);
	}
	
	public function replaceEqualsByHTML($text){
		return preg_replace("/=/","&#61;",$text);
	}
	
	public static function replaceUnsecureWithWildcards($text){
	    return preg_replace("/[^a-zA-Z0-9]/", "_", $text);
	}
}