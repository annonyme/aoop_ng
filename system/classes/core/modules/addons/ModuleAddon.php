<?php

namespace core\modules\addons;

class ModuleAddon
{

    private $clazz = '';
    private $moduleCallname = '';
    private $name = '';
    private $configFilePath = '';
    private $templatePath = '';
    private $autoStartup = false;

    public function getClazz(): string
    {
        return $this->clazz;
    }

    public function setClazz(string $clazz)
    {
        $this->clazz = $clazz;
    }

    public function getModuleCallname(): string
    {
        return $this->moduleCallname;
    }

    public function setModuleCallname(string $moduleCallname)
    {
        $this->moduleCallname = $moduleCallname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getConfigFilePath(): string
    {
        return $this->configFilePath;
    }

    public function setConfigFilePath(string $configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

    public function isAutoStartup(): bool
    {
        return $this->autoStartup;
    }

    public function setAutoStartup(bool $autoStartup)
    {
        $this->autoStartup = $autoStartup;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;
    }
}