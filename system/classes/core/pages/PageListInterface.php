<?php
namespace core\pages;

interface PageListInterface{
	public function loadByFolder($path);
	public function addPage($page);
	public function getSize();
	public function getPage($index);
	public function getPageByName($name);
	public function getPageByAlias($alias, $locale = null);
	public function existsIn($page);
	public function getAsList();
}