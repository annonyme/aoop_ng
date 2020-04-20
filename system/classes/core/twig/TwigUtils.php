<?php
namespace core\twig;

class TwigUtils {
    public static function findNewBase($template, $list = [], $currentBase = null) {
        $result = 'base';
        $found = $currentBase == null; //if null, took the first one
        foreach($list as $key => $path) {
            if($key == $currentBase) {
                $found = true;
            }
            else if ($found && file_exists($path . '/' . $template)) {
                $result = $key;
                break;
            }
        }

        return $result;
    }
}
