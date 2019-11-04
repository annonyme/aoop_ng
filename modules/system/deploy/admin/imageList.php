<?php
use core\net\XWRequest;
use core\utils\config\GlobalConfig;
use core\utils\XWServerInstanceToolKit;

$request = XWRequest::instance()->getRequestAsArray();


if($_SESSION["XWUSER"]->isInGroup(["pagesAdmins", "instanceAdmins", "admins"])){
    $currentInstance = XWServerInstanceToolKit::instance()->getCurrentInstanceName();
    $root = GlobalConfig::instance()->getValue("imagesfolder") . $currentInstance . "/uploads/";
    if(!is_dir($root)){
        mkdir($root, 0777, true);
    }

    $data = [];
    if(is_dir($root)){
        $dir = new DirectoryIterator($root);
        foreach ($dir as $file){
            if(!$file->isDot() && $file->isFile()){
                if(preg_match("/((\.jpg)|(\.png)|(\.jpeg)|(\.gif))$/i", $file->getFilename())){
                    $data[] = [
                        'title' => $file->getFilename(),
                        'value' => $root . $file->getFilename(),
                    ];
                }
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($data);
    die();
}


