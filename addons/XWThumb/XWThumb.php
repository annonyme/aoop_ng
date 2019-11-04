<?php
/*
 * Created on 23.08.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
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
 
class XWThumb{
	public function __construct(){
		
	}
	
	public function onTheFly($image,$thumbWidth=60,$type=""){
		$this->saveThumbnailToFile($image,$thumbWidth,"",$type);
	}
	
	public function simpleScale($pic,$targetWidth){
		$width = imagesx($pic);
	    $height = imagesy($pic);
	    $twidth = $targetWidth; 
	    $theight = $twidth * $height / $width; // calculate height
	    $thumb = @imagecreatetruecolor ($twidth, $theight) or die ("Can't create Image!");
	    imagecopyresized($thumb, $pic, 0, 0, 0, 0, $twidth, $theight, $width, $height); //resize image into thumb
	    return $thumb;		
	}
	
	public function saveThumbnailToFile($image,$thumbWidth=60,$destFile="",$type=""){
		if(function_exists("gd_info")){		
	        $pic=null;
	        if(preg_match("/^.+\.jp(e)?g$/i",$image)){
	        	$pic = @imagecreatefromjpeg($image) or die ("Image not found!");
	        	if($type==""){
	        		$type="jpeg";
	        	}        	
	        }
	        else if(preg_match("/^.+\.png$/i",$image)){
	        	$pic = @imagecreatefrompng($image) or die ("Image not found!");
	        	if($type==""){
	        		$type="png";
	        	} 
	        } 
	        else if(preg_match("/^.+\.gif$/i",$image)){
	        	$pic = @imagecreatefromgif($image) or die ("Image not found!");
	        	if($type==""){
	        		$type="gif";
	        	} 
	        } 
	        else if(preg_match("/^.+\.bmp$/i",$image)){
	        	$pic = @imagecreatefromwbmp($image) or die ("Image not found!");
	        	if($type==""){
	        		$type="bmp";
	        	} 
	        }        
	        if ($pic!=null) {
	            $width = imagesx($pic);
	            $height = imagesy($pic);
	            $twidth = $thumbWidth; //width of the thumb 60 pixel by default
	            $theight = $twidth * $height / $width; // calculate height
	            $thumb = @imagecreatetruecolor ($twidth, $theight) or die ("Can't create Image!");
	            imagecopyresized($thumb, $pic, 0, 0, 0, 0, $twidth, $theight, $width, $height); //resize image into thumb
	            if($destFile==""){
					if($type!=""){
						header ("Content-type: image/".$type);
					}
					else{
						header ("Content-type: image/jpeg");
					}				
				}
	            if($type=="jpeg"){
	            	ImageJPEG($thumb,$destFile,85); //Thumbnail as JPEG, maybe later as something else too..
	            }
	            elseif($type=="png"){
	            	imagepng($thumb,$destFile);
	            }
	            elseif($type=="gif"){
	            	imagegif($thumb,$destFile);
	            }
	            else{
	            	ImageJPEG($thumb,$destFile,85); //Thumbnail as JPEG, maybe later as something else too..
	            }            
	        }
		}
		else{
			die ("Can't create Image! gd not installed");
		}
	}
} 
?>
