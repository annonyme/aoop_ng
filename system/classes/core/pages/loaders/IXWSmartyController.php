<?php
namespace core\pages\loaders;

use core\utils\XWLocalePropertiesReader;

interface IXWSmartyController{
	public function process(array $request,XWLocalePropertiesReader $dict);
}