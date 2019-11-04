<?php
namespace core\pages\loaders;

class XWPageLoaderResult{
	private $pageContent = '';
	private $titleAdd = '';
	private $metaDescription = '';

	private $noRendering = false;
	
	public function __construct(){
		
	}
	
	public function getPageContent() {
		return $this->pageContent;
	}
	
	public function setPageContent($pageContent) {
		$this->pageContent = $pageContent;
		return $this;
	}
	
	public function getTitleAdd() {
		return $this->titleAdd;
	}
	
	public function setTitleAdd($titleAdd) {
		$this->titleAdd = $titleAdd;
		return $this;
	}

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription(string $metaDescription)
    {
        $this->metaDescription = $metaDescription;
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
}
