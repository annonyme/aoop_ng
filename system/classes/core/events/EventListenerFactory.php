<?php

namespace core\events;

use core\modules\factories\XWModuleListFactory;
use core\modules\XWModule;
use Exception;
use core\modules\XWModuleList;
use hannespries\events\EventHandler;
use PDBC\PDBCCache;
use ReflectionClass;

class EventListenerFactory extends EventHandler
{
    private $dbName = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init();
    }

    public function initByFolderList($list, $dbName = null)
    {
        $this->clear();
        $this->dbName = $dbName;
        foreach ($list as $folder) {
            if (!preg_match('/\/$/', $folder)) {
                $folder .= '/';
            }
            if (is_file($folder . 'deploy/listeners.json')) {
                $json = json_decode(file_get_contents($folder . 'deploy/listeners.json'), true);
                if (isset($json['listeners']) && is_array($json['listeners'])) {
                    foreach ($json['listeners'] as $listener) {
                        if (!isset($listener['scope'])) {
                            $listener['scope'] = 'on';
                        }

                        if (!isset($listener['method'])) {
                            $listener['method'] = 'on' . ucfirst($listener['alias']);
                        }

                        //{alias:,scope:,class:,method:}
                        $this->addListener($listener['alias'], $listener['class'], $listener['method'], $listener['scope']);
                    }
                }
            }
        }
    }

    /**
     * @param XWModuleList|null $modules
     */
    public function init($modules = null)
    {
        if ($modules === null && class_exists('core\\modules\\factories\\XWModuleListFactory')) {
            $modules = XWModuleListFactory::getFullModuleList()->toArrayList();
        }
        if ($modules !== null) {
            /** @var XWModule $module */
            foreach ($modules as $module){
                if (is_file($module->getPath() . '/deploy/listeners.json')) {
                    $json = json_decode(file_get_contents($module->getPath() . '/deploy/listeners.json'), true);
                    if (isset($json['listeners']) && is_array($json['listeners'])) {
                        foreach ($json['listeners'] as $listener) {
                            if (!isset($listener['scope'])) {
                                $listener['scope'] = 'on';
                            }

                            if (!isset($listener['method'])) {
                                $listener['method'] = 'on' . ucfirst($listener['alias']);
                            }

                            //{alias:,scope:,class:,method:}
                            $this->addListener($listener['alias'], $listener['class'], $listener['method'], $listener['scope']);
                        }
                    }
                }
            }
        }
    }

    /**
     * for before/after events on a method call
     *
     * perform('saveuser', 'save', $user, [])
     * perform('loadpost', 'load', $post, [$id])
     *
     * @param string $alias name of the event on that the liseners are registered
     * @param string $method mehod that is called on the object
     * @param mixed $obj target object on that the method is called and the called is listened to
     * @param array $args argument for the method call on the object
     * @param bool $usePDBCTransaction like an EJB
     *
     * @return mixed
     */
    public function perform(string $alias, string $method = null, $obj, array $args = [], bool $usePDBCTransaction = false, $getReturnValue = false)
    {
        try {
            if ($usePDBCTransaction && $this->dbName) {
                PDBCCache::getInstance()->getDB($this->dbName)->beginTransaction();
            }
            if ($method) {
                $objTmpBefore = $this->call($alias, $obj, 'before', $args);
                if ($objTmpBefore) {
                    $obj = $objTmpBefore;
                }

                //perform action
                $ref = new ReflectionClass($obj);
                $m = $ref->getMethod($method);
                $returnValue = $m->invokeArgs($obj, $args);
                $args['returnValue'] = $returnValue;

                $obj = $this->call($alias, $obj, 'after', $args);
            } else {
                $obj = $this->call($alias, $obj, 'on', $args);
                $args['returnValue'] = $obj;
            }
            if ($usePDBCTransaction && $this->dbName) {
                PDBCCache::getInstance()->getDB($this->dbName)->commit();
            }
        } catch (Exception $e) {
            if ($usePDBCTransaction && $this->dbName) {
                PDBCCache::getInstance()->getDB($this->dbName)->rollback();
            }
            $args['_exception'] = $e;
            $this->call($alias, $obj, 'error', $args);
        }
        return $getReturnValue ? $args['returnValue'] : $obj;
    }
}