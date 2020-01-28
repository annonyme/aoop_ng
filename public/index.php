<?php

/*
 * Copyright (c) 2008/2009/2010/2011/2014/2015/2016/2017/2019 Hannes Pries <http://www.hannespries.de>
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

use core\twig\TwigFunctions;
use core\utils\config\GlobalConfig;
use core\router\XWURLGenericFunctionListener;
use core\router\XWURLGenericControllerListener;
use core\modules\resources\XWModuleResourceLoader;
use core\modules\factories\XWModuleListFactory;

use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCDBFactory;
use core\net\rest\XWRESTServiceLoader;

use core\pages\loaders\XWFastPostProPageLoader;

use core\pages\plain\XWPage;
use core\modules\XWModuleClassLoader;

use core\addons\Services;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use core\utils\displayMessages\DisplayMessageFactory;

//include composer autoloader
$autoloader = require_once("../vendor/autoload.php");
$autoloader->addPsr4("core\\", "../system/classes/core/");

//load global-config
GlobalConfig::instance("../globalconfig.json");
$autoloader->addPsr4("PDBC\\", GlobalConfig::instance()->getValue("pdbcclassesfolder"));

//include modules classes
XWModuleClassLoader::addPsr4($autoloader);

session_start();

//reset classpath to clear all errors in classapth after changes
if (isset($_REQUEST["clearClassPath"]) && $_REQUEST["clearClassPath"] == "true") {
    $_SESSION["XWCLASSCACHE"] = [];
    unset($_SESSION["XW_MODULE_LIST_FULL_CACHING"]);
    XWModuleListFactory::reset();
}

//XWServerSwitch
//---------------------------------------------------------------------------
$toolKit = XWServerInstanceToolKit::instance();
$switcher = $toolKit->getServerSwitch();

//create db-connection  (PDBC V 2.0)
//---------------------------------------------------------------------------
$pdbcConfFolder = GlobalConfig::instance()->getValue("configspath") . "pdbc/";
PDBCDBFactory::init($pdbcConfFolder);

$dbName = XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
Services::getContainer()->set('db', \PDBC\PDBCCache::getInstance()->getDB($dbName));

//TODO Fixing!!!!!!
try {
    $config = new \Doctrine\DBAL\Configuration();
    $connectionParams = [
        'url' => XWServerInstanceToolKit::instance()->getEnvValues()['doctrineUrl'],
    ];
    //     $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    //     Services::getContainer()->set('dbal_connection', $conn);

    //     $pathes = [];
    //     $moduleList = XWModuleListFactory::getFullModuleList();
    //     for($i = 0; $i < $moduleList->getSize(); $i++){
    //         $mod = $moduleList->getModule($i);
    //         $pathes[] = $mod->getPath() . '/deploy/classes/';
    //     }
    //     $configORM = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($pathes, true);
    //     $entityManager = \Doctrine\ORM\EntityManager::create($conn, $configORM);
    //     Services::getContainer()->set('em', $entityManager);
} catch (Exception $e) { }

//define locale
//--------------------------------------------------------------------------- 
if (Services::getContainer()->get("XWLocale") != null) {
    $addon = Services::getContainer()->get("XWLocale");
    if ($addon instanceof XWLocale) {
        /** @var XWLocale $addon */
        $addon->findLocale();
    }
}

//pre init system-services (from singletons to services)
Services::getContainer()->set('events', \core\events\EventListenerFactory::getInstance());
Services::getContainer()->set('systemLogger', \core\logging\XWLoggerFactory::getLogger(\core\utils\XWServerSwitch::class));
Services::getContainer()->set('mailer', \core\mail\SMTPMailerFactory::instance());
Services::getContainer()->set('instance', XWServerInstanceToolKit::instance());
Services::getContainer()->set('autoloader', $autoloader);
Services::getContainer()->set('pageDir', XWServerInstanceToolKit::instance()->getServerSwitch()->getPages());

//it is a default addon, so we can use it here
if (class_exists('DisplayMessageFactory')) {
    Services::getContainer()->set('messages', DisplayMessageFactory::instance());
}


//load content (order: resource, REST, include theme)
//---------------------------------------------------------------------------
$pageLoadResult = null;
if (isset($_REQUEST["_resource"]) && ($_REQUEST["_resource"] == "bypage" || $_REQUEST["_resource"] == "bymodule")) {
    if ($_REQUEST["_resource"] == "bypage") {
        cmsPageContent($_REQUEST, "", "", true);
    } else {
        $loader = new XWModuleResourceLoader();
        $loader->directOutputOfResource($_REQUEST["page"], $_REQUEST["resource"]);
    }
} else if (isset($_REQUEST["_userestfront"]) && intval($_REQUEST["_userestfront"]) == 1) {
    $calledUrl = $_SERVER["REQUEST_URI"];
    $loader = new XWRESTServiceLoader();
    echo $loader->process($calledUrl, $_REQUEST);
} else {
    //** @deprecated: replaced bei routeSEO */
    $route = [];
    $route["pattern"] = "/\/((index.php)|(\/idx-))?/i";
    $route["target"] = [];
    $route["target"]["type"] = "function";
    $route["target"]["function"] = "getPageContent";
    $route["target"]["args"] = [];
    $route["target"]["args"][] = ["type" => "_request"];
    $route["target"]["args"][] = ["type" => "string", "requestvalue" => "true", "pattern" => "page"];
    $route["target"]["args"][] = ["type" => "string", "requestvalue" => "true", "pattern" => "sub"];

    $routeSEO = [];
    $routeSEO["pattern"] = "/^.+\/([a-zA-Z0-9_\-]+)\.html$/Ui";
    $routeSEO["target"] = [];
    $routeSEO["target"]["type"] = "function";
    $routeSEO["target"]["function"] = "getPageContent";
    $routeSEO["target"]["args"] = [];
    $routeSEO["target"]["args"][] = ["type" => "_request"];
    $routeSEO["target"]["args"][] = ["type" => '', "group" => 1];
    $routeSEO["target"]["args"][] = ["type" => "_null"];

    $router = new core\router\XWURLRouter();
    if (is_file(XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'static_routes.json')) {
        $router->addPatternFromJSONFile('staticroutes', XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'static_routes.json');
    }
    $moduleList = XWModuleListFactory::getFullModuleList();
    for ($i = 0; $i < $moduleList->getSize(); $i++) {
        $mod = $moduleList->getModule($i);
        $router->addPatternFromJSONFile($mod->getCallName(), $mod->getPath() . "/deploy/routes.json");
    }
    $router->addPattern('defaultSEO', $routeSEO);
    $router->addPattern("default", $route);

    $router->addListener(new XWURLGenericControllerListener());
    $router->addListener(new XWURLGenericFunctionListener());
    Services::getContainer()->set('router', $router);



    $resolveResult = $router->resolve(null, $_REQUEST);
    /** @var \core\pages\loaders\XWPageLoaderResult $pageLoadResult */
    $pageLoadResult = $resolveResult->getContent();
    if (intval($resolveResult->getCode()) > 0) {
        http_response_code($resolveResult->getCode());
    }

    //prepared for twig-rendering
    $pageModel = [
        'content' => $pageLoadResult->getPageContent(),
        'title' => $pageLoadResult->getTitleAdd(),
        'metadescription' => $pageLoadResult->getMetaDescription(),
        'url' => $_SERVER["REQUEST_URI"],
    ];
 	     try{
             $loader = new FilesystemLoader(
                 [
                     $themeFullPath,
                     XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . GlobalConfig::instance()->getValue("instancepagefolder"),
                 ]
             );
             $twig = new Environment($loader);
             $twig = TwigFunctions::decorateTwig($twig);

    /**
     * load theme from system or module
     */
    $theme = $switcher->getTheme();
    $themesPath = GlobalConfig::instance()->getValue("themespath");
    $themeFullPath = null;
    if (isset($_REQUEST["adminpage"]) && ($_REQUEST["adminpage"] == "1" || $_REQUEST["adminpage"] == "true") && $switcher->getAdminTheme() != "") {
        $theme = $switcher->getAdminTheme();
        $themeFullPath = $themesPath . $theme;
    } else {
        $themeFullPath = $themesPath . $theme;
        $result = \hannespries\events\EventHandler::getInstance()->fireFilterEvent('theme_load_by_name', ['path' => null], ['theme' => $theme, 'admin' => false]);
        if (isset($result['path']) && file_exists($result['path'])) {
            $themesPath = $result['path'];
        }
    }

    if ($pageLoadResult->isNoRendering()) {
        if (strlen($pageLoadResult->getContentType()) > 0) {
            header('Content-Type: ' . $pageLoadResult->getContentType());
        }
        echo $pageLoadResult->getPageContent();
    } else if (is_dir($themeFullPath)) {

        try {
            $loader = new \Twig_Loader_Filesystem(
                [
                    $themeFullPath,
                    XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . GlobalConfig::instance()->getValue("instancepagefolder"),
                ]
            );
            $twig = new \Twig_Environment($loader);
            $twig = \core\twig\TwigFunctions::decorateTwig($twig);


            $model = ['request' => \core\net\XWRequest::instance()->getRequestAsArray()];
            $model['env'] = XWServerInstanceToolKit::instance()->getEnvValues();
            $model['page'] = $pageModel;
            if (isset($_SESSION["XWUSER"])) {
                $model['user'] = $_SESSION["XWUSER"];
            }

            $outputString = '';
            try {
                $outputString = $twig->render(GlobalConfig::instance()->getValue("thememainfile"), $model);
            } catch (\Exception $e) { }
            echo $outputString;
        } catch (Exception $e) { }
    } else if (file_exists($themeFullPath)) {
        include_once($themeFullPath);
    } else {
        //output error
        echo "<html><body>theme-<strong>file not found</strong>, check deploy.xml of current instance.</body></html>";
    }
}


//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//System functions (aoop-API)
//------------------------------------------------------------------------------

/**
 * @return \core\pages\loaders\XWPageLoaderResult
 * @param null|array $request
 * @param string $page
 * @param string $sub
 */
function getPageContent($request = null, $page = null, $sub = null)
{
    $toolKit = XWServerInstanceToolKit::instance();
    $switcher = $toolKit->getServerSwitch();
    $pageDir = Services::getContainer()->get('pageDir');

    if ($request == null) {
        $request = $_REQUEST;
    }

    if ($page == null) {
        if (!isset($request["page"])) {
            if (isset($request["adminpage"]) && !isset($request["sub"])) {
                $page = "system";
            } else {
                $page = $switcher->getHomepage();
            }
        } else {
            $page = preg_replace("/\//", "_", $request["page"]);
            $page = preg_replace("/\.\./", "_", $page);
            $page = preg_replace("/^http/", "_", $page);
            $page = preg_replace("/^ftp/", "_", $page);
        }
        $request["page"] = $page;
    } else if (!isset($request['page'])) {
        $_REQUEST['page'] = $page;
        $request['page'] = $page;
    }

    if ($sub == null) {
        if (isset($request["sub"])) {
            $sub = preg_replace("/\//", "_", $request["sub"]);
            $sub = preg_replace("/\.\./", "_", $sub);
            $sub = preg_replace("/^http/", "_", $sub);
            $sub = preg_replace("/^ftp/", "_", $sub);
        } else {
            $sub = "index";
        }
        $request["sub"] = $sub;
    }

    //--------
    $loader = new XWFastPostProPageLoader();
    $loader->setPageDir($pageDir);
    $loader->setModuleDir("modules/");
    return $loader->loadPage($page, $sub, $request, isset($request["adminpage"]));
}

// load Page Content.. include page or module from pages-, global-module- or admin-folder
function cmsPageContent($directPrint = false)
{
    /** @var \core\pages\loaders\XWPageLoaderResult $pageLoadResult */
    global $pageLoadResult;
    if ($pageLoadResult == null || $directPrint) {
        echo getPageContent()->getPageContent();
    } else {
        echo $pageLoadResult->getPageContent();
    }
}
