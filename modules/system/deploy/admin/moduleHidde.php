<?php
/*
 * Created on 03.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\modules\XWModuleList;
use core\net\XWRequest;
use core\utils\XWServerInstanceToolKit;

$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins")){
    $list=new XWModuleList();
    if(isset($request["instance"])){
    	if($request["instance"]=="instance"){
    		$list->load(XWServerInstanceToolKit::instance()->getServerSwitch()->getPages());
    	}
    	else{
    		$list->load();
    	}
    }
    else{
    	$list->load();
    }    
    
    $module=null;
    if(isset($request["callName"])){
    	$module=$list->getModuleByCallName($request["callName"]);
    }
    
    if($module!=null){
    	$module->hidde();
    }
    

?>
<div class="PresentationBoxHeader">Toggle hidden-status of <?=$module->getName() ?>:</div>
<div class="PresentationBox">
  <?php
    if($module->isHidden()){
    	echo "was make visible.";
    }
    else{
    	echo "was make invisible.";
    }    
  ?>
</div>
<?php
}else{
?>

<?php
}
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=moduleList">Module-Administration</a></div>
