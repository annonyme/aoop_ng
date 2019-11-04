<?php
/*
 * Created on 14.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$content=file_get_contents(".htaccess");
$calledUrl=preg_replace("/patchHtaccess.php/","",$_SERVER["REQUEST_URI"]);
$calledUrl=preg_replace("/^\//","",$calledUrl);
$content=preg_replace("/\S*index.php/",$calledUrl."index.php",$content);
file_put_contents(".htaccess",$content);
?>
.htaccess patched!
