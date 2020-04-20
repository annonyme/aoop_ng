<?php 
namespace core\router;

use Exception;
use ReflectionClass;

/**
 * {
 * pattern:
		* target:{
			* type:
			* _specificvalue_:
			* args:[{pattern:,type:,group:,requestvalue:,onnull:}]
		* }
		* subroutes:[]
	* }
 *
* if subroutes are set, the target is optional
 *
* use patterns:[{...},{....}] to use more than one route in json-files
 * @author annonyme
 *
 */
class XWURLRouter{
	
	private $patterns = [];
	
	/**
	 * @var XWURLRouterListener[]
	 */
	private $listeners = [];
	
	public function __construct(){
		
	}
	
	public function addListener(XWURLRouterListener $listener){
		$this->listeners[] = $listener;
	}
	
	public function addPattern($name, $pattern){
		$this->patterns[$name] = $pattern;
	}
	
	public function addPatternFromJSONFile($name, $file){
		if(file_exists($file)){
			$json = json_decode(file_get_contents($file), true);
			if($json){
				if(isset($json['listeners'])){
				    foreach ($json['listeners'] as $listener){
				        try{
                            $clazz = new ReflectionClass($listener);
                            $obj = $clazz->newInstance();
                            if($obj instanceof XWURLRouterListener){
                                $this->addListener($obj);
                            }
                        } catch(Exception $e){

                        }
                    }
                }
			    if(isset($json["patterns"])){
					foreach($json["patterns"] as $key => $pattern){
						$this->patterns[$name . "_" . ($key + 1)] = $pattern;
					}
				}
				else{
					$this->patterns[$name] = $json;
				}				
			}
		}
	}
	
	public function resolve($url = null, $request = []){
		if($url === null){
			$url = "http" . (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] ? "S" : "") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 
		}
		
		$result = null;
		foreach($this->patterns as $pattern){
			$tmpResult = $this->parsePattern($pattern, $url, $request);
			if($tmpResult !== null){
				$result = $tmpResult;
				break;
			}
		}
		return $result;
	}
	
	/**
	 * @return XWURLResolveResult
	 * @param array $pattern
	 * @param string $url
	 * @param array $request
	 */
	private function parsePattern($pattern, $url, $request = []){
		$result = null;
		try{
			if(preg_match($pattern["pattern"], $url)){
				if(isset($pattern["target"])){
					$args = XWURLArgumentResolver::resolve($pattern["target"]["args"], $pattern["pattern"], $url, $request);
					foreach ($this->listeners as $listener){
						if($listener->checkType($pattern["target"]["type"])){
							$result = $listener->call($pattern["target"], $args);
							if(isset($pattern["target"]["mime"])){
								$result->setMime($pattern["target"]["mime"]);
							}
                            if(isset($pattern["target"]["statuscode"])){
                                $result->setCode($pattern["target"]["statuscode"]);
                            }
							break;
						}
					}					
				}
				if(isset($pattern["subroutes"]) && is_array($pattern["subroutes"])){
					foreach($pattern["subroutes"] as $subpattern){
						$subresult = $this->parsePattern($subpattern, $url, $request);
						if($subresult !== null){
							$result = $subresult;
							break;
						}
					}
				}
            }
		}
		catch(Exception $e){
			
		}
		return $result;
	}
}