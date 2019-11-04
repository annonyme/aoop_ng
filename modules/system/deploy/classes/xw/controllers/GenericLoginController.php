<?php
namespace  xw\controllers;

use core\modules\controllers\XWModulePageController;
use core\modules\controllers\XWModulePageRenderingResult;
use core\net\XWRequest;
use xw\entities\users\XWUserDAO;

class GenericLoginController extends XWModulePageController{
    protected $redirectClass = "";
    protected $redirectTemplate = "index.html";
    
    private function redirect(XWModulePageRenderingResult $result):XWModulePageRenderingResult{
        if(strlen($this->redirectClass) > 0 && class_exists($this->redirectClass)){
            $refClass = new \ReflectionClass($this->redirectClass);
            $controller = $refClass->newInstance();
            $result = $controller->result();
            $result->setAlternativeTemplate($this->redirectTemplate);
        }
        return $result;
    }
    
    public function result(){
        $result=new XWModulePageRenderingResult();
        $model=[];
        
        if(XWUserDAO::instance()->isCurrentUserValid()){
            $this->redirect($result);
        }
        else{
            $model['username'] = '';
            if(XWRequest::instance()->exists('username')){                
                $model['username'] = preg_replace("/\"/", "&#34;", XWRequest::instance()->get('username'));
            }
        }
        
        $result->setModel($model);
        return $result;
    }
}