<?php
namespace core\modules\controllers;

use core\utils\XWLocalePropertiesReader;
use core\modules\XWModule;

abstract class XWModulePageController{
	private $dictionary = null;
	private $module = null;
	
	/**
	 * @return XWModulePageRenderingResult
	 */
	public abstract function result():XWModulePageRenderingResult;
	
	/**
	 * @return XWLocalePropertiesReader
	 */
	public function getDictionary():XWLocalePropertiesReader{
		return $this->dictionary;
	}
	
	/**
	 * @param XWLocalePropertiesReader $dictionary
	 */
	public function setDictionary(XWLocalePropertiesReader $dictionary) {
		$this->dictionary = $dictionary;
	}

	/**
	 * @return XWModule
	 */
    public function getModule(): XWModule
    {
        return $this->module;
    }

    /**
     * @param XWModule $module
     */
    public function setModule(XWModule $module)
    {
        $this->module = $module;
    }
 
	
	
}