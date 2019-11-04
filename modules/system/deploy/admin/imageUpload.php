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
    if(isset($_FILES["upfile"]) && preg_match("/((\.jp(e)?g)|(\.png)|(\.bmp)|(\.gif))$/i",$_FILES["upfile"]["name"])){
        $imgfile=$_FILES["upfile"]["tmp_name"];
        $finalFilename=$_FILES["upfile"]["name"];

        copy($imgfile,$root. $finalFilename);

        $data = [
            'location' => $root . $finalFilename,
        ];
    }

    header("Content-Type: application/json");
    echo json_encode($data);
    die();
}