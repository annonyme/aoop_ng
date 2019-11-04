<?php
namespace core\datastorage\ng;

use core\utils\filesystem\XWFileList;
use core\utils\filesystem\XWSubDirList;

class NativeFS implements FileHandler{
    private $root = null;
    private $publicURI = null;
    private $sep = '/';

    public function __construct($root, $publicURI, $user, $credentials, $params = []){
        $this->root = $root;
        if(preg_match("/\\\/", $root)){
            $this->root = preg_replace("/\\\/", '/', $root);
        }
        $this->publicURI = $publicURI;
    }

    private function cwFolder($folder){
        if(preg_match("/\\\/", $folder)){
            $folder = preg_replace("/\\\/", '/', $folder);
        }
        return $this->root . $this->sep;
    }


    public function saveFile($source, $targetFolder, $targetFilename)
    {
        file_put_contents($this->cwFolder($targetFolder) . $this->sep . $targetFilename, file_get_contents($source));
    }

    public function appendToFile($content, $targetFolder, $targetFilename)
    {
        file_put_contents($this->cwFolder($targetFolder) . $this->sep . $targetFilename, $content, FILE_APPEND);
    }

    public function loadFile($folder, $filename)
    {
        return file_get_contents($this->cwFolder($folder) . $this->sep . $filename);
    }

    public function delete($folder, $filename)
    {
        unlink($this->cwFolder($folder) . $this->sep . $filename);
    }

    public function existsFile($folder, $filename)
    {
        return is_file($this->cwFolder($folder) . $this->sep . $filename);
    }

    public function createPublicURI($folder, $filename)
    {
        return $this->publicURI . $folder . '/' . $filename;
    }

    public function createFolder($folder)
    {
        mkdir($this->cwFolder($folder) , 0777, true);
    }

    public function fileList($folder)
    {
        $list = new XWFileList();
        $list->load($this->cwFolder($folder));
        return $list;
    }

    public function folderList($folder)
    {
        $list = new XWSubDirList();
        $list->load($this->cwFolder($folder));
        return $list;
    }
}