<?php
namespace core\pages;

interface XWCallableContent{
    public function getName();    
    
    public function getCallName();
    
    public function getParent();

    public function getRedirectLink();
}