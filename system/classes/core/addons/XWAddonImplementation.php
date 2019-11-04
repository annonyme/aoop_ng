<?php
namespace core\addons;

use core\logging\XWLogger;

class XWAddonImplementation extends XWRenderingAddon {
    /** @var XWLogger */
    protected $logger;
    /** @var XWAddonManager */
    protected $addonManager;
    protected $path;

    //to auto render within twig-templates
    public function render($vars = []):string {
        return '';
    }

    /**
     * @return XWLogger
     */
    public function getLogger(): XWLogger
    {
        return $this->logger;
    }

    /**
     * @param XWLogger $logger
     */
    public function setLogger(XWLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return XWAddonManager
     */
    public function getAddonManager(): XWAddonManager
    {
        return $this->addonManager;
    }

    /**
     * @param XWAddonManager $addonManager
     */
    public function setAddonManager(XWAddonManager $addonManager)
    {
        $this->addonManager = $addonManager;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}