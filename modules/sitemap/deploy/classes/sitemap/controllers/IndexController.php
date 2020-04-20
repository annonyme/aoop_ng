<?php
namespace sitemap\controllers;

use core\addons\Services;
use core\events\EventListenerFactory;
use core\modules\controllers\XWModulePageController;
use core\modules\controllers\XWModulePageRenderingResult;
use core\modules\factories\XWModuleListFactory;
use core\modules\XWModuleDeployer;
use core\net\XWRequest;
use core\pages\plain\XWPageListFactory;
use core\utils\XWServerInstanceToolKit;
use Exception;
use xw\entities\users\XWUser;

class IndexController extends XWModulePageController{

    /**
     * @return XWModulePageRenderingResult
     */
    public function result(): XWModulePageRenderingResult
    {
        $result = new XWModulePageRenderingResult();
        try{
            $model = ['items' => []];

            $pageList=XWPageListFactory::getFullPageList();
            $pages=$pageList->getAsList();

            $itk=XWServerInstanceToolKit::instance();
            $url=$itk->getCurrentInstanceURL();

            $user=new XWUser();

            //pages list + get last change date from real file
            for($i=0;$i<$pages->size();$i++){
                $page=$pages->get($i);
                $time=date("Y-m-d",filemtime($page->getPath()));

                $model['items'][] = [
                    'loc' => $page->getParent() ? $url . $page->getParent() . '/' . $page->getCallName() : $url . $page->getCallName(),
                    'changefreq' => 'weekly',
                    'lastmod' => $time,
                    'priority' => $page->getParentPage()=="" ? 1 : 0.8,
                ];
            }

            //modules list + get changefreq from module (last change.. later.. TODO.. subpage to request date?)
            $modules=XWModuleListFactory::getFullModuleList();
            for($i=0;$i<$modules->getSize();$i++){
                $module=$modules->getModule($i);
                if(!$module->isHidden() && $module->hasUserPermission($user) && $module->getCallName()!= XWRequest::instance()->get("page")){
                    //sub menu
                    $deployer=new XWModuleDeployer();
                    $deployer->load($module);
                    $contSize=$deployer->getSize();

                    $moduleItemUrls = [$url . $module->getCallName() . '.html'];
                    for($j=0;$j<$contSize;$j++){
                        $item=$deployer->getSubPageMenuItem($j);
                        if(!$item->isOnlyVisibleWithLogin()){
                            $moduleItemUrls[] = $url . "index.php?page=" . $module->getCallName() . "%26sub=" . $item->getLinkedPage();
                        }
                    }

                    /** @var EventListenerFactory $events */
                    $events = Services::getContainer()->get('events');
                    $moduleItemUrls = $events->fireFilterEvent('Sitemap_Module_Collection_' . $module->getCallName(), $moduleItemUrls, ['mainurl' => $url]);

                    foreach ($moduleItemUrls as $itemUrl){
                        $model['items'][] = [
                            'loc' => $itemUrl,
                            'changefreq' => $module->getChangeFrequence(),
                            'priority' => 0.8,
                        ];
                    }
                }
            }

            $model['items'][] = [
                'loc' => $url . 'index.php',
                'changefreq' => 'weekly',
                'priority' => 1,
            ];

            $result->setModel($model);
        }
        catch(Exception $e){

        }
        $result->setNoRendering(true);
        return $result;
    }
}