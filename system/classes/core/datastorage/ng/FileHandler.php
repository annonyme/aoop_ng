<?php

namespace core\datastorage\ng;

interface FileHandler{
    public function saveFile($source, $targetFolder, $targetFilename);
    public function appendToFile($content, $targetFolder, $targetFilename);
    public function loadFile($folder, $filename);
    public function delete($folder, $filename);
    public function existsFile($folder, $filename);

    public function createPublicURI($folder, $filename);

    public function createFolder($folder);
    public function fileList($folder);
    public function folderList($folder);
}
