<?php

namespace core\database;

/*
 * Created on 29.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
  * Copyright (c) 2008/2012/2019 Hannes Pries <https://www.hannespries.de>
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

class XWSQLSecure
{
  public function removeSemicolonsFromNonStringParts($sql, $replace = " ")
  {
    $func = function ($a) {
      return "'" . preg_replace("/;/", '__semicol__', $a[1]) . "'";
    };

    $sql = preg_replace("/__semicol__/", "", $sql);
    $sql = preg_replace_callback("/\'(.*)\'/Uis", $func, $sql);
    $sql = preg_replace("/;/", $replace, $sql);
    $sql = preg_replace("/__semicol__/", ";", $sql);

    $func2 = function ($a) {
      return "'" . preg_replace("/;/", '__comment__', $a[1]) . "'";
    };

    $sql = preg_replace("/__comment__/", "", $sql);
    $sql = preg_replace_callback("/\'(.*)\'/Uis", $func2, $sql);
    $sql = preg_replace("/\-\-+/", $replace, $sql);
    $sql = preg_replace("/__comment__/", "--", $sql);
    return $sql;
  }

  public function getHighlightedSQLinHTML($sql)
  {
    $func = function ($a) {
      return "'" . preg_replace("/;/", '__semicol__', $a[1]) . "'";
    };

    $sql = preg_replace_callback("/\'(.*)\'/Uis", $func, $sql);
    $sql = preg_replace("/;/", ";<br/>\n", $sql);
    $sql = preg_replace("/__semicol__/", ";", $sql);
    $sql = preg_replace("/\'(.*)\'/Uis", "<span style=\"color:#FF0000;\">'$1'</span>", $sql);
    $sql = preg_replace("/(select)/Uis", "<span style=\"color:#00FF00;font-style:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(insert\s+into)/Uis", "<span style=\"color:#00FF00;font-weight:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(delete)/Uis", "<span style=\"color:#00FF00;font-weight:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(from)/Uis", "<span style=\"color:#0000FF;font-weight:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(where)/Uis", "<span style=\"color:#0000FF;font-weight:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(order\s+by)/Uis", "<span style=\"color:#0000FF;font-weight:bold;\">$1</span>", $sql);
    $sql = preg_replace("/(values)/Uis", "<span style=\"color:#0000FF;font-weight:bold;\">$1</span>", $sql);
    return $sql;
  }

  public function removeSingleQuotes($text)
  {
    return preg_replace("/\'/", "", $text);
  }

  public function replaceSingleQuotesByHTML($text)
  {
    return preg_replace("/(\\\)?\'/", "&#39;", $text);
  }

  public function replaceDoubleQuotesByHTML($text)
  {
    return preg_replace("/(\\\)?\"/", "&#34;", $text);
  }

  public function replaceEscapesByHTML($text)
  {
    return preg_replace("/(\\\)/", "&#92;", $text);
  }

  public function replaceSemicolonsByHTML($text)
  {
    return preg_replace("/;/", "&#59;", $text);
  }

  public function replaceWildcardsByHTML($text)
  {
    return preg_replace("/%/", "&#37;", $text);
  }

  public function replaceEqualsByHTML($text)
  {
    return preg_replace("/=/", "&#61;", $text);
  }
}
