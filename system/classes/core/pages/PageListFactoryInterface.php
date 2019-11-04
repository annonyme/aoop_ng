<?php
namespace core\pages;

interface PageListFactoryInterface{
	public static function getFullPageList($pageDir = null);
}