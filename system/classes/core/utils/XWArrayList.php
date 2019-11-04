<?php
/*
 * Created on 13.11.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2016 Hannes Pries <http://www.annonyme.de>
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
  
class XWArrayList implements \Iterator{
	
	private $list=[];
	private $counter=0; //added to minimize the usage of count() on the internal array
	
	/**
	 * Something like the Java ListArray..
	 * but not fully implemented List-Interface..
	 * @param array $arr
	 */
	public function __construct($arr = []){
		$this->list = $arr;
        $this->counter=count($this->list);
	}
	
	public function add($object){
		$this->list[$this->counter]=$object;
		$this->counter=count($this->list);
	}
	
	public function size(){
		return $this->counter;
	}
	
	public function get($index){
		return $this->list[$index];
		
	}
	
	public function clear(){
		$this->list=[];
		$this->counter=count($this->list);
	}
	/**
	 * {@inheritDoc}
	 * @see Iterator::current()
	 */
	public function current() {
		return current($this->list);
	}

	/**
	 * {@inheritDoc}
	 * @see Iterator::next()
	 */
	public function next() {
		return next($this->list);
	}

	/**
	 * {@inheritDoc}
	 * @see Iterator::key()
	 */
	public function key() {
		return key($this->list);
	}

	/**
	 * {@inheritDoc}
	 * @see Iterator::valid()
	 */
	public function valid() {
		return $this->current() !== false;
	}

	/**
	 * {@inheritDoc}
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		reset($this->list);
	}
	
	/**
	 * @return array
	 */
	public function toArray(): array{
	    return $this->list;
	}
}
