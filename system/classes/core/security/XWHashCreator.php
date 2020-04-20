<?php
/*
 * Created on 28.04.2011
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

use core\utils\XWCodeGenerator;

class XWHashCreator{
	
	
	public function __construct(){
		
	}
	/**
	 * @return string
	 * @param string $password
	 */
	public static function createBCrypt($password){
		return password_hash($password, PASSWORD_BCRYPT, ['salt' => XWCodeGenerator::instance()->generate(23), 'cost' => 10]);
	}
	
	/**
	 * @return bool
	 * @param string $password
	 * @param string $hash
	 */
	public static function validateBCrypt($password, $hash){
		return password_verify($password, $hash);
	}
	
	public function userNameAsSaltEncode($password,$username){
		return md5($password.md5($username));
	}
	
	public function userNameAsSaltEncodeSHA1($password,$username){
		return sha1($password.sha1($username));
	}
	
	public function userNameAsSaltEncodeDoubleHash($password,$username){
		return md5(md5($password).md5($username));
	}
	
	/**
	 * takes a x-char long token from password, adds to string and creates hash,
	 * string = hash, takes the next x-char long token... and so on till the 
	 * and of the password. Counts round to validate password length and creates
	 * a final hash from endhash from recursive runs and rounds.
	 */
	public function multiSaltEncode($password,$hash="",$tokenLength=2,$rounds=0){
        $toHash = '';
        if (strlen($password) > $tokenLength) {
            /*
            for($i=0;$i<$tokenLength;$i++){
                $toHash.=substr($password,$i,1);
            }
            */
            $toHash = substr($password, 0, $tokenLength);
            //$password=preg_replace("/^".$toHash."/","",$password);
            $password = substr($password, $tokenLength, strlen($password));
        } else {
            $toHash = $password;
            $password = '';
        }

        if ($toHash == '') {
            return md5($rounds . $hash); //rounds to check that length of password is correct and no collision for this hash is used
        } else {
            //echo "to hash: <strong>".$toHash."</strong>| cutted password: ".$password."| result will be (of '".$hash."<strong>".$toHash."</strong>'): ".md5($hash.$toHash)."<br/>";
            $rounds++;
            return $this->multiSaltEncode($password, md5($hash . $toHash), $tokenLength, $rounds);
        }
	}
	
	/**
	 * takes a x-char long token from password, adds to string and creates hash,
	 * string = hash, takes the next x-char long token... and so on till the 
	 * and of the password. Counts round to validate password length and creates
	 * a final hash from endhash from recursive runs and rounds.
	 */
	public function multiSaltEncodeSHA1($password,$hash="",$tokenLength=2,$rounds=0){
		$toHash="";
		if(strlen($password)>$tokenLength){
			for($i=0;$i<$tokenLength;$i++){
				$toHash.=substr($password,$i,1);						
			}
			$password=preg_replace("/^".$toHash."/","",$password);
		}
		else{
			$toHash=$password;
			$password="";
		}
						
		if($toHash==""){
			return sha1($rounds.$hash); //rounds to check that length of password is correct and no collision for this hash is used
		}
		else{
			//echo "to hash: <strong>".$toHash."</strong>| cutted password: ".$password."| result will be (of '".$hash."<strong>".$toHash."</strong>'): ".md5($hash.$toHash)."<br/>";
			$rounds++;
			return $this->multiSaltEncodeSHA1($password,sha1($hash.$toHash),$tokenLength,$rounds);
		}
	}
	
	/**
	 * post-scrambling for longer runtime (but you should use the method with rounds)
	 */
	public function scrambledMultiSaltEncodeSHA1($password,$hash="",$tokenLength=2){
		$tk=new XWScramblingToolKit();
		return $tk->simpleScrambling($this->multiSaltEncodeSHA1($password,$hash,$tokenLength));
	}
	
	/**
	 * post-scrambling for longer runtime
	 */
	public function scrambledMultiSaltEncodeSHA1WithRounds($password,$rounds=25,$salt="",$tokenLength=2){
		$tk=new XWScramblingToolKit();
		return sha1($rounds.$tk->simpleScramblingWithRounds($this->multiSaltEncodeSHA1($password,sha1($salt.$rounds),$tokenLength),$rounds));
	}
	
	private function createCheckSumHash($text){
		$check=0;
		for($i=0;$i<strlen($text);$i++){
			$check+=ord(substr($text,$i,1));
		}
		return sha1($check);
	}
} 
