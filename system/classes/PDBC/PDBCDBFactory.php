<?php
/*
 * Copyright (c) 2014 Hannes Pries <http://www.annonyme.de>
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
 
namespace PDBC;

use ReflectionClass;
 
class PDBCDBFactory {
	private $confPath = "conffiles/";
	
	public function __construct($confPath = "conffiles/") {
		$this->confPath = $confPath;
		if(!is_dir($confPath)){
			throw new \Exception("pdbs config folder not found: ".$confPath);
		}		
		PDBCCache::init($this);
	}
	
	public static function init($confPath = "conffiles/"){
		new PDBCDBFactory($confPath);
	}
	
	public function getDB($dsName) {
		$ds = new PDBCDataSource($dsName, $this->confPath."datasources.xml");
		if (class_exists($ds->getClassName(), true)) {
			$ref=new ReflectionClass($ds->getClassName());
			return $ref->newInstanceArgs(array($ds->getJDBCURL(),$ds->getUserName(),$ds->getUserPassword()));
		} else {
			echo "database-implementation not existing (".$ds->getClassName().")";
			return null;
		}
	}
}