<?php
namespace core\pages;

interface PageInterface{
	public function getAlias();
	public function getIntlAlias($locale);
	public function hasIntlAlias($alias);
	public function getCallName(); //old function for alias
	public function getName();
	public function getNames();
	public function getPath();
	public function getFolder();
	public function isHidden();
	public function getParentAlias();
	public function getParentPageCallName(); //old function for alias
	public function getKeywords();
	public function getTitle();
	public function getMetaDescription();
	public function getChangefreq();
	
	public function readName($locale);
	public function load($folder, $alias);
	
	public function toArray();
	public function toJson();
}