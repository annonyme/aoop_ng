<?php
namespace  core\addons;

class Services {
    public static function getContainer(){
        return XWAddonManager::instance();
    }
}