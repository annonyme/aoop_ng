<?php
namespace parallax;

use core\pages\loaders\XWPageLoaderResult;
use core\pages\plain\XWPage;
use core\pages\plain\XWPageListFactory;
use Exception;
use Twig\Environment;

class Listener{
    /**
     * @param XWPageLoaderResult $result
     * @param array $args
     *
     * @throws Exception
     */
    public function onPage($result, $args = []){
        /** @var XWPage $page */
        $page = $args['page'];
        $model = $args['model'];
        /** @var Environment $twig */
        $twig = $args['twig'];

        $sep = ''; //'<div class="parallax"></div>';

        $subs = [$page];
        $pages = XWPageListFactory::getFullPageList();
        for($i = 0; $i < $pages->getSize(); $i++){
            $item = $pages->getPage($i);
            if($item->getParent() == $page->getCallName() && strlen($item->getParent()) > 0){
                $subs[] = $item;
            }
        }

        if(count($subs) > 1){
            $res = '';
            foreach ($subs as $key => $sub){
                if($key > 0){
                    $res .= '<div class="parallax parallax-seperator"></div>';
                }

                $res .= '
                    <div class="content-container container-fluid">
                        <div class="col-md-2"></div>
                        <div id="pageContent" class="col-xs-12 col-sm-12 col-md-8">
                            ' . $twig->render($sub->getCallName() . '.html', $model) . '
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                ';
            }
            $result->setPageContent($sep . $res . $sep);
        }
        else{
            $result->setPageContent( $sep . '
                    <div class="content-container container-fluid">
                        <div class="col-md-2"></div>
                        <div id="pageContent" class="col-xs-12 col-sm-12 col-md-8">
                            ' . $result->getPageContent() . '
                        </div>
                        <div class="col-md-2"></div>
                    </div>' . $sep
            );
        }
    }
}