<?php
/*
 * Created on 20.07.2011
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

namespace xw\entities\users;

use core\utils\XWArrayList;
 
class XWUserToolKit{
	
	public function __construct(){
		
	}
	
	/**
	 * find @userName tokens in text
	 */
	public function findUserNamesInText($text){
		return $this->findUserNamesInTextByValidCheck($text);
	}

    /**
     * @param $text
     * @param null|XWUserList $checkAgainstValidUserList
     *
     * @return XWArrayList
     */
	public function findUserNamesInTextByValidCheck($text,$checkAgainstValidUserList=null){
		$userNames=new XWArrayList();
		
		$stringPattern=preg_replace("/(\@.+)([\s\,\:])/Uis","_username_$2",$text);
		$parts=preg_split("/_username_/Uis",$stringPattern);
		for($i=0;$i<count($parts);$i++){
			if($parts[$i]!=""){
				$text=preg_replace("/".$parts[$i]."/Uis",",",$text);
			}
		}
		
		$users=preg_split("/\,/Uis",$text);
		for($i=0;$i<count($users);$i++){
			if(trim($users[$i])!=""){
				if($checkAgainstValidUserList!=null){
					if($checkAgainstValidUserList->existsInByName(trim(preg_replace("/\@/Uis","",$users[$i])))){
						$userNames->add(trim(preg_replace("/\@/Uis","",$users[$i])));
					}
				}
				else{
					$userNames->add(trim(preg_replace("/\@/Uis","",$users[$i])));
				}				
			}				
		}
		
		return $userNames;
	}
} 
