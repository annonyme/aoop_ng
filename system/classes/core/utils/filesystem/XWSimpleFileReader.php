<?php
 /*
  * Copyright (c) 2007 Hannes Pries <http://www.annonyme.de>
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
 
 //macht eigentlich das selbe wie file_get_content()
 
namespace core\utils\filesystem;

class XWSimpleFileReader{
	private $fileContent="";

	/**
	 * im constructor wird die Datei, wenn vorhanden, eingelesen
	 */ 
	public function __construct($file){
		if(file_exists($file)){
			$this->fileContent=file_get_contents($file);
		}
		else{
			echo "\n<!-- file nicht da! [".$file."] -->\n";
		}
	}

	/**
	 * gibt den inhalt der Datei zurück
	 */
	public function getContent(){
		return $this->fileContent;
	}

	/**
	 * tauscht \n durch einen beliebigen string aus und gibt das ergebnis zurück
	 */
	public function getContentChangeLineSeperator($newSeperator){
		return preg_replace("/\n/",$newSeperator,$this->fileContent);	
	}
}
