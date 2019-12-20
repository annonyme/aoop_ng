<?php
namespace core\modules\controllers;

use core\utils\XWLocalePropertiesReader;

class XWModulePageRenderingResult{
	private $model = [];
	private $dict = null;
	private $alternativeTemplate = null;
	
	private $title = '';

	private $noRendering = false;
	private $contentType = 'text/html';
	
	/**
	 * @return array
	 */
	public function getModel() {
		return $this->model;
	}
	
	/**
	 * @param array $model
	 */
	public function setModel($model) {
		$this->model = $model;
	}
	
	/**
	 * @return XWLocalePropertiesReader|null
	 */
	public function getDict() {
		return $this->dict;
	}
	
	/**
	 * @param XWLocalePropertiesReader|null $dict
	 */
	public function setDict($dict) {
		$this->dict = $dict;
	}
	
	/**
	 * @return string|null
	 */
	public function getAlternativeTemplate() {
		return $this->alternativeTemplate;
	}
	
	/**
	 * @param string|null $alternativeTemplate
	 */
	public function setAlternativeTemplate($alternativeTemplate) {
		$this->alternativeTemplate = $alternativeTemplate;
	}

    public function getTitle():string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function isNoRendering(): bool
    {
        return $this->noRendering;
    }

    /**
     * @param bool $noRendering
     */
    public function setNoRendering(bool $noRendering)
    {
        $this->noRendering = $noRendering;
	}
	
	/**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }
}