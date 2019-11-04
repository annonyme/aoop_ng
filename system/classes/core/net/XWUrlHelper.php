<?php
/*
 * Copyright (c) 2016 Hannes Pries <http://www.annonyme.de>
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

namespace core\net;

use core\logging\XWLoggerFactory;
use core\logging\XWLogger;

class XWUrlHelper{
	
	private $request=null;
	
	private static $instance = null;
	
	/**
	 * @return XWUrlHelper
	 */
	public static function instance(){
		if(self::$instance==null){
			self::$instance = new XWUrlHelper($_REQUEST);
		}
		return self::$instance;
	}
	
	public function __construct($request){
		$this->request=$request;
	}
	
	private function createFullPageURL(){
		$url="http";
		if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]){
			$url.="s";
		}
		$url.="://" . $_SERVER["SERVER_NAME"];
		$parts = preg_split("/[\/]/", $_SERVER["PHP_SELF"]);
		for($i=0; $i<count($parts)-1; $i++){
			$url.=$parts[$i]."/";
		}
		$url.="index.php";
		return $url;
	}
	
	/** 
	 * @param string $module
	 * @param string $page
	 * @return string
	 */
	private function createPagePart($module,$page){
		$result="";
		if($module!=null && strlen($module)>0){
			if($page==null || strlen($page)==0){
				$page="index";
			}
			
			$result="page=".$module."&sub=".$page;
		}
		else{
			$result="page=".$page;
		}
		return $result;
	}
	
	/**
	 * @param array $map
	 * @return string
	 */
	private function mapToString($map){
		$result="";
		foreach($map as $key => $value){
			if(strlen($result)>0){
				$result.="&";
			}
			$result.=$key."=".$value;
		}
		return $result;
	}
	
	/** 
	 * @param string $module
	 * @param string $page
	 * @param array $params
	 * @return string
	 */
	public function buildPageURL($module,$page,$params,$adminPage=false){
		$admin="";
		if($adminPage){
			$admin="&adminpage=1";
		}
		return "index.php?".$this->createPagePart($module,$page)."&".$this->mapToString($params).$admin;
	}
	
	public function redirectToPage($module,$page,$params,$adminPage=false){
		$admin="";
		if($adminPage){
			$admin="&adminpage=1";
		}
		$url=$this->createFullPageURL()."?".$this->createPagePart($module,$page)."&".$this->mapToString($params).$admin;
		
		
		try{
			ob_clean();
			$file = "";
			$line = 0;
			$sent = headers_sent($file, $line);			
			if(!$sent){
				header("Location: ".$url, true, 302);
			}
			else{
				XWLoggerFactory::getLogger(XWUrlHelper::class)->log(XWLogger::ERROR, "header already sent: ".$file." :: ".$line);
			}
			exit;
		}
		catch(\Exception $e){
			XWLoggerFactory::getLogger(XWUrlHelper::class)->log(XWLogger::ERROR, $e->getMessage(), $e);
		}		
	}
	
	/** 
	 * @param string $page
	 * @param array $params
	 * @return string
	 */
	public function buildCurrentModulePageURL($page,$params){
		$module="";
		if(isset($this->requestT["page"]) && isset($this->requestT["sub"])){
			$module=$this->requestT["page"];
		}
		return $this->buildPageURL($module, $page, $params, false);
	}
	
	/**
	 * @param string $module
	 * @param string $resource
	 * @return string
	 */
	public function buildResourceURL($module,$resource){
		return "index.php?page=".$module."&_resouce=bymodule&resouce=".$resource;
	}
	
	/**
	 * @param string $module
	 * @param string $page
	 * @param array $params
	 * @return string
	 */
	public function buildPageResourceURL($module,$page,$params,$adminPage=false){
		$admin="";
		if($adminPage){
			$admin="&adminpage=1";
		}
		return "index.php?".$this->createPagePart($module,$page)."&".$this->mapToString($params)."&_resource=bypage".$admin;
	}
	
	/**
	 * @param string $text
	 */
	public static function buildBaseUrlForReadable($text){
		return "idx-".preg_replace("/[^a-zA-Z0-9]/i", "-", strtolower($text)).".html";
	}

	public static function simplyfyText($text, $id = '', $fileExt = '.html', $module = null){
	    $text = strtolower($text);
	    $text = preg_replace("/[äÄ]/", 'ae', $text);
        $text = preg_replace("/[üÜ]/", 'ue', $text);
        $text = preg_replace("/[öÖ]/", 'oe', $text);
        $text = preg_replace("/[ß]/", 'ss', $text);
        $text = preg_replace("/[^a-z0-9_\-]/", '-', $text);
        $text = preg_replace("/\-{2,}/", '', $text);
        $text .= '-' . $id . $fileExt;
        if($module){
            $text = $module . '/' . $text;
        }
        return $text;
    }
}