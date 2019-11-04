<?php
/*
 * Created on 03.08.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
/*
  * Copyright (c) 2011/2015 Hannes Pries <http://www.annonyme.de>
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

use xw\entities\messages\XWMessage;
use xw\entities\users\XWUser;
use core\datastorage\XWDataStorageFactory;
use core\utils\config\GlobalConfig;
 
class XWImageGrabingCache extends \core\addons\XWAddonImplementation{
	
	private $destinationDS="public_assets";
	private $compression="75";
	private $ds=null;
	private $reportUserName=""; //send pm to this user if getimagesize returns 0
	
	private $noFileByUrl=false;
	
	public function __construct(){
		try{
            $storeFactory=new XWDataStorageFactory(GlobalConfig::instance()->getValue("configspath")."datastorage.xml");
            $this->ds=$storeFactory->getDataStoreByName($this->destinationDS);
        }
        catch(Exception $e){

        }
		
		if(strtoupper(ini_get("allow_url_fopen"))!="ON" && ini_get("allow_url_fopen")!=1){
			$this->noFileByUrl=true;
		}
		else{
			$this->noFileByUrl=false;
		}
	}
	
	private function saveImage($url,$destWidth="60"){
		$pic=null;
	    if(preg_match("/\.jp(e)?g$/i",$url)){
	        $pic = @imagecreatefromjpeg($url);
	    }
		else if(preg_match("/\.png$/i",$url)){
		    $pic = @imagecreatefrompng($url);
		}
		else if(preg_match("/\.gif$/i",$url)){
		    $pic = @imagecreatefromgif($url);
		}
		else{
			$pic = @imagecreatefromjpeg($url);
			if($pic==null){
				$pic = @imagecreatefrompng($url);
			}
		}
		
		if($pic!=null && getimagesize($url)>0){			
			$width = imagesx($pic);
            $height = imagesy($pic);
            $twidth = $destWidth; //width of the thumb
            $theight = $twidth * $height / $width; // calculate height
            $resizedPic = @imagecreatetruecolor ($twidth, $theight) or die ("Can't create Image!");
            imagecopyresized($resizedPic, $pic, 0, 0, 0, 0, $twidth, $theight, $width, $height);
			
            if(!is_dir($this->ds->getLoadFullPath())){
            	mkdir($this->ds->getLoadFullPath(),0777,true);
            }
			ImageJPEG($resizedPic,$this->ds->getLoadRelativePath()."grab_".md5($url.$destWidth).".jpg",$this->compression);
		}
		else{
			if(getimagesize($url)==0 && $this->reportUserName!=""){
				//send security pm to user
				$user=new XWUser();
				$user->loadByName($this->reportUserName);
				if($user->getId()>0){
					$pm=new XWMessage();
					$pm->setReceiverId($user->getId());
					$pm->setUserId($user->getId());
					$pm->setTitle("security problem in imagegrabingcache");
					$activeUserName=" *_not_logged_in_* ";
					if(isset($_SESSION["XWUSER"])){
						$activeUserName=$_SESSION["XWUSER"]->getName();
					}
					$pm->setContent("_".$url."_ is no image. active user was: ".$activeUserName);
					$pm->save();
				}				
			}
		}		
	}
	
	private function imageExistinsInCache($url,$width){
		return $this->ds->fileExists("grab_".md5($url.$width).".jpg");
	}
	
	public function findUrl($url,$width="60"){
		$isURL=false;
		//filesystem path don't starts with "http..."
		if(preg_match("/^http/i",$url)){
			$isURL=true;
		}
		//could an image be load from an url? or only from filesystem		
		if($this->noFileByUrl && $isURL){
			return $url;
		}
		else{
			if(!$this->imageExistinsInCache($url,$width)){
				$this->saveImage($url,$width);
			}
			if(!$this->imageExistinsInCache($url,$width)){
				if(getimagesize($url)>0){
					return $url;
				}
				else{
					return "#"; //insecure
				}			
			}
			else{
				return $this->ds->getLoadRelativePath()."grab_".md5($url.$width).".jpg";
			}
		}		
	}
	
	public function printUrl($url,$width="60"){
		echo $this->findUrl($url,$width);
	}
	
	public function setCompression($compression="75"){
		$this->compression=$compression;
	}
	
	public function setDestinationDS($destinationDS="temp"){
		$this->destinationDS=$destinationDS;
	}
	
	public function setReportUserName($reportUserName){
		$this->reportUserName=$reportUserName;
	}

    public function render($vars = []): string{
	    return $this->findUrl($vars['url'], (int) $vars['width']);
    }
} 
