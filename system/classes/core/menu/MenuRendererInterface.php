<?php
namespace core\menu;

interface MenuRendererInterface{
	/**
	 * @return string
	 * @param MenuItem[] $items
	 */
	public function render($items = []);
}