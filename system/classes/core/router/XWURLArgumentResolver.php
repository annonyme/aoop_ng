<?php 
namespace core\router;

use Exception;

class XWURLArgumentResolver{
	public static function resolve($args = [], $globalPattern, $url, $request = []){
		$result = [];
		foreach($args as $arg){
		    $value = null;
			if(isset($arg['staticvalue']) && $arg['staticvalue']){
			    $value = $arg['staticvalue'];
            }
            else{
                $pattern = isset($arg["pattern"]) && strlen($arg["pattern"]) > 0 ? $arg["pattern"] : $globalPattern;
                if(isset($arg["requestvalue"]) && strtolower($arg["requestvalue"]) == "true"){
                    $value = isset($request[$pattern]) ? $request[$pattern] : null;
                }
                else if($arg["type"] == "_request"){
                    $value = $request;
                }
                else if($arg["type"] == "_session"){
                    $value = $_SESSION;
                }
                else if($arg["type"] == "_null"){
                    $value = null;
                }
                else{
                    $group = isset($arg["group"]) && intval($arg["group"]) > 1 ? intval($arg["group"]) : 1;
//                    $value = preg_replace($pattern, "$".$group, $url);

                    $values = [];
                    preg_match_all($pattern, $url, $values);
                    $value = $values[$group][0];

                    $value = self::convertToType($value, isset($arg["type"]) ? $arg["type"] : "string");
                }
            }
			
			if($value === null && isset($arg["onnull"])){
				$value = self::convertToType($arg["onnull"], isset($arg["type"]) ? $arg["type"] : "string");
			}

			$result[] = $value;
		}
		return $result;
	}
	
	private static function convertToType($value, $type = "string"){
		$result = $value;
		try{
			if($type == "int"){
				$result = (int) $value;
			}
			else if($type == "float"){
				$result = (float) $value;
			}
			else if($type == "int_abs"){
				$result = abs((int) $value);
			}
			else if($type == "bool" || $type == "boolean"){
				$result=strtolower($value)=="true" || intval($value)==1;
			}
			else if($type == "base64"){
				$result = base64_decode($value);
			}
			else if($type == "encoded_string"){
				$result = urldecode($value);
			}
			else if($type == "encoded_json_string"){
				$result = json_decode(urldecode($value));
			}
		}
		catch(Exception $e){
			
		}		
		return $result;
	}
}