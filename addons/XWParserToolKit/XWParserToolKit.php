<?php
/*
 * Created on 27.11.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2008/2010/2011/2013/2015 Hannes Pries <http://www.annonyme.de>
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

use core\parser\XWParserToolKitRule;
 
class XWParserToolKit{
	private $css="";
	private $rules="";
	/**
	 * @var XWParserToolKitRule[] $parserRules
	 */
	private $parserRules=[];
	private $useOwnCss="false";
	
	private $addonManager=null;
	
	public function __construct(){
		
	}
	
	public function setAddonManager($addonManager){
		$this->addonManager=$addonManager;
	}
	
	private function readInParserRules(){		
		$xml=preg_replace("/#\+/","<![CDATA[",$this->rules);
		$xml=preg_replace("/\+#/","]]>",$xml);
		$doc = new DOMDocument(); 
		$doc->loadXML("<rules>".$xml."</rules>");
		$rules=$doc->getElementsByTagName("rule");
		$rule=null;
		$ruleObj=null;
		foreach($rules as $rule){
			$ruleObj=new XWParserToolKitRule();
			
			$nodes=$rule->childNodes;
			$node=null;
			foreach ($nodes as $node){	
				if($node->nodeName=="pattern" && $node->nodeValue!=""){
					$patCont=$node->nodeValue;
					if(preg_match("/_USER_/",$patCont) && isset($_SESSION["XWUSER"])){
						$patCont=preg_replace("/_USER_/",$_SESSION["XWUSER"]->getName(),$patCont);
					}					
					$ruleObj->setPattern($patCont);	
				}
				else if($node->nodeName=="value" && $node->nodeValue!=""){
					$ruleObj->setValue($node->nodeValue);
				}
				else if($node->nodeName=="varname" && $node->nodeValue!=""){
					$ruleObj->setVarname($node->nodeValue);
				}
				else if($node->nodeName=="description" && $node->nodeValue!=""){
					$ruleObj->setDescription($node->nodeValue);
				}
			}
			
			if($ruleObj->getPattern()!=""){
				$this->parserRules[count($this->parserRules)]=$ruleObj;
			}			
		}
	}
	
	public function printCSSCode(){
		if($this->useOwnCss!="true"){
			echo $this->css;
		}		
	}
	
	public function getCSSCode(){
	    $css = "";
	    if($this->useOwnCss!="true"){
	        $css = $this->css;
	    }
	    return $css;
	}
	
	public function convertUTF8($text){
		if(mb_detect_encoding($text)!="utf-8"){
			$text=utf8_encode($text);
		}	
		return $text;
	}	
	
	public function parseBBCode($text,$parseRules=true){
		if(strlen($text)>0){
			$count=count($this->parserRules);
			$text=preg_replace("/&#61;/","=",$text);
			$text=preg_replace("/&#39;/","'",$text);
			if($parseRules){
				if($count==0){
					$this->readInParserRules();
					$count=count($this->parserRules);
				}
				$rule=null;
				for($i=0;$i<$count;$i++){
					$rule=$this->parserRules[$i];
					//echo "debug: ".$rule->getPattern()." --  <br/>\n";
					$text=preg_replace($rule->getPattern(),$rule->getValue(),$text);
					if($rule->getVarname()!="" && $this->addonManager!=null){
						$text=preg_replace("/__".$rule->getVarname()."__/",$this->addonManager->getAddonByName("XWServerInstanceInfos")->getInfoByName($rule->getVarname()),$text);
						$text=preg_replace("/&amp;/","&",$text);
					}
				}
			}			
		}		
		return $text;
	}
	
	public function removeBBCode($text){
		if(strlen($text)>0){
			$count=count($this->parserRules);
			$text=preg_replace("/&#61;/","=",$text);
			$text=preg_replace("/&#39;/","'",$text);
			if($count==0){
				$this->readInParserRules();
				$count=count($this->parserRules);
			}
			$rule=null;
			for($i=0;$i<$count;$i++){
				$rule=$this->parserRules[$i];
				$text=preg_replace($rule->getPattern(),"",$text);
			}
		}
		return $text;
	}
	
	public function disableHtml($html){
		if(strlen($html)>0){
			$html=preg_replace("/</i","&lt;",$html);
        	$html=preg_replace("/>/i","&gt;",$html);
        	$html=preg_replace("/(\\\)?\"/","&#34;",$html);
        	$html=preg_replace("/\\$/","&#x24;",$html);
		}
        return $html;
	}
	
	public function secureHTMLForSaving($html){
		if(strlen($html)>0){
			$html=preg_replace("/<\s*(\/)?\s*script/i","&ltscript;",$html);
			$html=preg_replace("/(\\\)?\"/","&#34;",$html);
			$html=preg_replace("/\\$/","&#x24;",$html);
		}
		return $html;
	}
	
	public function recreateFromSecuredHTML($html){
		if(strlen($html)>0){
			$html=preg_replace("/&#34;/","\"",$html);
			$html=preg_replace("/&#61;/","=",$html);
		}
		return $html;
	}
	
	public function onlyNumbers($text){
		return preg_replace("/[^0-9]/","",$text);
	}
	
	public function onlyDecimalNumbers($text){
		$amount=preg_replace("/,/i",".",$text);
		$sign="";
		if(preg_match("/^-/",$amount)){
			$sign="-";
		}
		$amount=preg_replace("/[^0-9.]/i","",$amount);
		return $sign.$amount;
	}
	
	/**
	 * @deprecated ...use disableHtml()
	 */
	public function htmlDisabler($html){
        return $this->disableHtml($html);
    }
    
    public function textUrlToLink($text){
    	$text=preg_replace("/(^|\s)+((http:\/\/)?(www\.))(.+)(\s|$)+/Uis"," <a href=\"http://$4$5\">http://$4$5</a> ",$text);
        return $text;
    }
    
    public function stringMaxLengthCutter($text,$maxLength="25",$newLineMarker="_"){
    	$shortText=preg_replace("/\s+/Uis"," ",$text);
        $shortText=preg_replace("/<br(\/)?>/Uis"," ".$newLineMarker." ",$shortText);
        $shortText=preg_replace("/^(.{1,".($maxLength-3)."}).*$/","$1",$shortText);
        if(strlen($text)>($maxLength-3)){
        	$shortText.="...";
        }
        return $shortText;
    }
    
    public function replaceUmlauts($text){
		$text=preg_replace("/�/","ue",$text);
		$text=preg_replace("/�/","ae",$text);
		$text=preg_replace("/�/","oe",$text);
		$text=preg_replace("/�/","Ue",$text);
		$text=preg_replace("/�/","Ae",$text);
		$text=preg_replace("/�/","Oe",$text);
		$text=preg_replace("/[�]/","ss",$text);
		return $text;
	}
	
	public function replaceUmlautsByHTMLEntites($text){
		$text=preg_replace("/�/","&uuml;",$text);
		$text=preg_replace("/�/","&auml;",$text);
		$text=preg_replace("/�/","&ouml;",$text);
		$text=preg_replace("/�/","&Uuml;",$text);
		$text=preg_replace("/�/","&Auml;",$text);
		$text=preg_replace("/�/","&Ouml;",$text);
		$text=preg_replace("/[�]/","&szlig;",$text);
		return $text;
	}
	
	public function insertStringEveryNthPosition($text,$token,$count="3",$runRevers=false){
		if($runRevers){
			return strrev(preg_replace("/(\d{".$count."})/","$1".$token."",strrev($text)));
		}
		else{
			return preg_replace("/(\d{".$count."})/","$1".$token."",$text);
		}
	}
	
	public function countWords($text){
		$result=0;
		$text=preg_replace("/[^a-zA-Z0-9]/is"," ",$text);
		$text=preg_replace("/\s+/is"," ",$text);
		$result=substr_count($text," ");
		return $result;
	}
	
	public function replaceNewLinesWithHTMLBreaks($text){
		return preg_replace("/\n/","<br/>",$text);
	}
	
	public function printRulesDescriptionList(){
		?>
		<ul class="xwparser-rules">
			<?php 
			foreach($this->parserRules as $rule){
				if($rule->getDescription()!=""){
					?>
					<li><?=$rule->getDescription() ?></li>
					<?php	
				}	
			}	
			?>
		</ul>
		<?php
	}
	
	public function getCss(){
		return $this->css;
	}
	
	public function setCss($css){
		$this->css=$css;
	}
	
	public function getRules(){
		return $this->rules;
	}
	
	public function setRules($rules){
		$this->rules=$rules;
	}
	
	public function setUseOwnCss($useOwnCss){
		$this->useOwnCss=$useOwnCss;
	}
	
	public function getUseOwnCss(){
		return $this->useOwnCss;
	}
} 
