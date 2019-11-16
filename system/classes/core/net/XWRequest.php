<?php
/*
 * Copyright (c) 2016/2019 Hannes Pries <http://www.annonyme.de>
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

use core\utils\XWServerInstanceToolKit;
use Symfony\Component\HttpFoundation\Request;

class XWRequest{	
	private $request=null;
	private $symfonyRequest = null;
	private $jsonRequest = false;
	private static $instance=null;
	
	static public function instance(){
		if(self::$instance==null){
			self::$instance=new XWRequest();
		}
		return self::$instance;
	}
	
	public function __construct(){
		if(count($_POST) == 0){
		    $raw =  file_get_contents("php://input");
		    if(preg_match("/^[\{\[]/", $raw)){
		        $_POST = json_decode($raw, true);
		        $_REQUEST = array_merge($_REQUEST, $_POST);
		        $this->jsonRequest = true;
            }
        }

        //TODO remove, use event
	    if(!isset($_REQUEST["page"])){
			$_REQUEST["page"]=XWServerInstanceToolKit::instance()->getServerSwitch()->getHomepage();
		}
		$this->request=$_REQUEST;

	    try{
            $this->symfonyRequest = new Request(
                $_GET,
                $_POST,
                array(),
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
        }
        catch(\Exception $e){

        }
	}

    /**
     * @return null|Request
     */
	public function getSymfonyRequest(){
	    return $this->symfonyRequest;
    }
	
	public function replaceRequest($request){
		$this->request=$request;
	}

    /**
     * @param string $name
     * @param string $value
     */
	public function set(string $name, string $value){
	    $this->request[$name] = $value;
    }
	
	/**
	 * @return string
	 * @param string $name
	 */
	public function get($name){
		return $this->getString($name);
	}
	
	/**
	 * @param string $name
	 * @param mixed $default
	 * @return null|string
	 */
	public function getString(string $name, $default = null): ?string {
		$result = $default;
		if(isset($this->request[$name])) {
			$result = $this->request[$name];
		}
		return $result;
	}
	
	/**
	 * @param string $name
	 * @param integer $default
	 * @return int
	 */
	public function getInt(string $name, int $default = 0): int{
		$result = $default;
		if(isset($this->request[$name])) {
			$result = (int) $this->request[$name];
		}
		return $result;
	}
	
	/**
	 * @return bool
	 * @param string $name
	 */
	public function getBoolean($name){
		return isset($this->request[$name]) && ($this->request[$name]==1 || strtolower($this->request[$name])=="true");
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function put($name,$value){
		$this->request[$name]=$value;
	}
	
	/**
	 * @return bool
	 * @param string $name
	 */
	public function exists($name){
		return isset($this->request[$name]);
	}
	
	/**
	 * @return array
	 */
	public function getRequestAsArray(){
		return $this->request;
	}

    /**
     * @return bool
     */
    public function isJsonRequest(): bool
    {
        return $this->jsonRequest;
    }

    /**
     * @param bool $jsonRequest
     */
    public function setJsonRequest(bool $jsonRequest)
    {
        $this->jsonRequest = $jsonRequest;
    }
}