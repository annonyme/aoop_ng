<?php

namespace core\router;

class XWURLResolveResult{
	private $type = "";
	private $content = null;
	private $exception = null;
	private $mime = "text/html";
	private $code = 200;
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getException() {
		return $this->exception;
	}
	
	public function setException($exception) {
		$this->exception = $exception;
	}
	
	public function getMime() {
		return $this->mime;
	}
	
	public function setMime($mime) {
		$this->mime = $mime;
	}

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }
}