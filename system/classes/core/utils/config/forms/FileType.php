<?php
namespace core\utils\config\forms;

interface FileType{
    public function checkFilename(string $filename):bool;
    public function loadAsArray(string $filename):array;
    public function saveArray(string $filename, $data = []);
}