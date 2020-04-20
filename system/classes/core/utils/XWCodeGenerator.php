<?php

/*
 * Created on 03.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
 * Copyright (c) 2008/2011/2016 Hannes Pries <http://www.annonyme.de>
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

namespace core\utils;

class XWCodeGenerator
{
	private static $instance = null;

	/**
	 * @return XWCodeGenerator
	 */
	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private $codeTable = [];
	public function __construct()
	{
		$codeTable = [];
		$codeTable[0] = "a";
		$codeTable[1] = "b";
		$codeTable[2] = "c";
		$codeTable[3] = "d";
		$codeTable[4] = "e";
		$codeTable[5] = "f";
		$codeTable[6] = "g";
		$codeTable[7] = "h";
		$codeTable[8] = "i";
		$codeTable[9] = "0";
		$codeTable[10] = "1";
		$codeTable[11] = "2";
		$codeTable[12] = "3";
		$codeTable[13] = "4";
		$codeTable[14] = "5";
		$codeTable[15] = "6";
		$codeTable[16] = "7";
		$codeTable[17] = "_";
		$codeTable[18] = "0";
		$codeTable[19] = "-";
		$codeTable[20] = "z";
		$codeTable[21] = "x";
		$codeTable[22] = "y";
		$codeTable[23] = "u";
		$codeTable[24] = "v";
		$codeTable[25] = "w";
		$codeTable[26] = "8";
		$codeTable[27] = "9";
		$codeTable[28] = "k";
		$codeTable[29] = "l";
		$codeTable[30] = "m";
		$codeTable[31] = "n";
		$codeTable[32] = "o";
		$codeTable[33] = "j";
		$codeTable[34] = "p";
		$codeTable[35] = "q";
		$codeTable[36] = "r";
		$codeTable[37] = "s";
		$codeTable[38] = "t";

		$this->codeTable = $codeTable;
	}

	/**
	 * simple random code generation
	 */
	public function generate($length = 10)
	{
		$code = '';
		for ($i = 0; $i < $length; $i++) {
			$code .= $this->codeTable[$this->getRandomNumber()];
		}
		return $code;
	}
	private function getRandomNumber()
	{
		return mt_rand(0, count($this->codeTable) - 1);
	}

	/**
	 * a more random as random code generation method
	 */
	public function generateWithInnerLoop($length = 10, $innerCount = 5)
	{
		$code = '';
		for ($i = 0; $i < $length; $i++) {
			$codes = new XWArrayList();
			for ($j = 0; $j < $innerCount; $j++) {
				$codes->add($this->codeTable[$this->getRandomNumber()]);
			}
			$code .= $codes->get(mt_rand(0, $codes->size() - 1));
		}
		return $code;
	}
}
