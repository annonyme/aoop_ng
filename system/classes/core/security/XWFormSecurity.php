<?php
namespace core\security;

use core\utils\XWCodeGenerator;

/**
 * https://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
 * @author annonyme
 */
class XWFormSecurity{
    public static function getRequestParameterName():string {
        return "_XW_SESSION_SEC";
    }
    
    public static function checkSessionSecToken($key=null):bool{        
        $result=false;
        if($key==null){
            if(isset($_REQUEST["_XW_SESSION_SEC"])){
                $result=$_SESSION["XWUSER_SESSSIONSEC"]==$_REQUEST["_XW_SESSION_SEC"];
            }
        }
        else{
            $result=$_SESSION["XWUSER_SESSSIONSEC"]==$key;
        }
        
        $_SESSION["XWUSER_SESSSIONSEC"]=XWCodeGenerator::instance()->generate(6);
        return $result;
    }
    
    public static function printHiddenInputWithSessionSecToken(){
        if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
            $_SESSION["XWUSER_SESSSIONSEC"]=XWCodeGenerator::instance()->generate(6);
        }
        echo "<input type=\"hidden\" name=\"_XW_SESSION_SEC\" value=\"".$_SESSION["XWUSER_SESSSIONSEC"]."\"/>";
    }
    
    public static function getSessionSecToken():string{
        if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
            $_SESSION["XWUSER_SESSSIONSEC"]=XWCodeGenerator::instance()->generate(6);
        }
        return $_SESSION["XWUSER_SESSSIONSEC"];
    }
    
    public static function getURLParameterWithSessionSecToken():string{
        if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
            $_SESSION["XWUSER_SESSSIONSEC"]=XWCodeGenerator::instance()->generate(6);
        }
        return "_XW_SESSION_SEC=".$_SESSION["XWUSER_SESSSIONSEC"]."";
    }
    
    public static function getURLParameterWithSessionSecTokenValueOnly():string{
        if(!isset($_SESSION["XWUSER_SESSSIONSEC"]) || strlen($_SESSION["XWUSER_SESSSIONSEC"])==0){
            $_SESSION["XWUSER_SESSSIONSEC"]=XWCodeGenerator::instance()->generate(6);
        }
        return $_SESSION["XWUSER_SESSSIONSEC"]."";
    }
}