<?php

namespace core\utils\json;

use ReflectionClass;

class XWJSONConverter
{
    public function __construct()
    {

    }

    /**
     * @param $value
     * @param int $level
     * @param null $name
     * @param bool $firstInLine
     * @param string $result
     * @return string
     * @throws \ReflectionException
     */
    public function convert($value, $level = 0, $name = null, $firstInLine = true, $result = '')
    {
        $opening = false;
        if ($level == 0 && $name != null) {
            $result .= '{';
            $opening = true;
        }
        if (is_scalar($value)) {
            if (!$firstInLine) {
                $result .= ',';
            }
            if ($name != null) {
                $result .= '"' . $name . '":';
            }
            $result .= json_encode($value);
        } else if (is_array($value)) {
            if ($name != null) {
                $result .= '"' . $name . '":';
            }
            $result .= '[';
            /*
            $cnt=count($value);
            for($i=0;$i<$cnt;$i++){
                $result=$this->convert($value[$i],$level+1,null,$i==0,$result);
            }
            */
            $i = 0;
            foreach ($value as $val) {
                $result = $this->convert($val, $level + 1, null, $i == 0, $result);
                $i++;
            }
            $result .= ']';
        } else if (is_object($value)) {
            if (!$firstInLine) {
                $result .= ',';
            }
            if ($name != null) {
                $result .= '"' . $name . '":';
            }
            $result .= '{';
            $ref = new ReflectionClass(get_class($value));
            $props = $ref->getProperties();
            $cnt = count($props);
            for ($i = 0; $i < $cnt; $i++) {
                $prop = $props[$i];

                $doc = trim($prop->getDocComment());
                if (!preg_match("/@json_transient/i", $doc)) {
                    $prop->setAccessible(true);
                    $name = $prop->getName();
                    if ($prop->isPrivate() || $prop->isProtected()) {
                        $name = preg_replace("/^_/", '', $name);
                    }
                    $result = $this->convert($prop->getValue($value), $level + 1, $name, $i == 0, $result);
                }
            }
            $result .= '}';
        } else if (is_null($value)) {
            if (!$firstInLine) {
                $result .= ',';
            }
            if ($name != null) {
                $result .= '"' . $name . '":';
            }
            $result .= 'null';
        }

        if ($opening) {
            $result .= '}';
        }
        return $result;
    }
}
