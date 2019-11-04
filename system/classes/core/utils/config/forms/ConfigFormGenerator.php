<?php
namespace core\utils\config\forms;

class ConfigFormGenerator{
    private $types = [];
    
    private static $instance = null;
    
    public static function instance():self{
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function getValue($key, $desc, $values){
        $result = isset($desc['default']) ? : '';
        if(isset($values[$key])){
            $result = $values[$key];
        }
        return $result;
    }
    
    public function generateFormContent(string $descriptor, string $file, string $section):string{
        $content = "";
        $values = [];
        foreach ($this->types as $type){
            /** @var FileType $type */
            if($type->checkFilename($file)){
                $data = $type->loadAsArray($file);
                if(isset($data[$section]) && is_array($data[$section])){
                    $values = $data[$section];
                }
            }
        }
        
        $desc = json_decode(file_get_contents($descriptor), true);
        
        foreach ($desc as $key => $field){
            $content .= "<label for=\"FORMGEN_".$key."\">" . $field['label'] . "</label>\n";
            if($field['type'] == "text"){                
                $content .= "<input type=\"text\" id=\"FORMGEN_".$key."\" name=\"FORMGEN_".$key."\" value=\"". $this->getValue($key, $field, $values) . "\"/>\n";
            }
            else if($field['type'] == "number"){
                //TODO
            }
            else if($field['type'] == "selection"){
                $content .= "<selection id=\"FORMGEN_".$key."\" name=\"FORMGEN_".$key."\" value=\"". $this->getValue($key, $field, $values) . "\">\n";
                if(isset($field['values']) && is_array($field['values'])){
                    foreach ($field['values'] as $val){
                        $content .= "<option value=\"" . $val['value'] . "\">" . $val['label'] . "</option>\n";
                    }
                }
                $content .= "</selection>\n";
            }
            else if($field['type'] == "range"){
                //TODO 
            }
            $content .= "<br/>\n";
        }
        
        return $content;
    }
    
    public function persistData(array $request=[], string $file, string $section){
        $values = [];
        foreach ($request as $key => $value){
            if(preg_match("/^FORMGEN_/", $key)){
                $realKey = preg_replace("/^FORMGEN_/", '', $key);
                $values[$realKey] = $value;
            }
        }
        
        foreach ($this->types as $type){
            /** @var FileType $type */
            if($type->checkFilename($file)){
                $data = $type->loadAsArray($file);
                $data[$section] = $values;
                $type->saveArray($file, $data);
            }
        }
    }
    
    public function addFileType(FileType $type){
        $this->types[] = $type;
    }
}