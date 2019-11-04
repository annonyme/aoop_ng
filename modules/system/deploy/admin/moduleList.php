<?php
/*
 * Created on 03.07.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
use core\modules\XWModuleList;
use core\modules\factories\XWModuleListFactory;
use core\net\XWRequest;

$request = XWRequest::instance()->getRequestAsArray();

if($_SESSION["XWUSER"]->isInGroup("admins")){
?>
<div class="PresentationBoxHeader">Modules:</div>
<div class="PresentationBox">
  <table>
    <?php        
    /** @var XWModuleList $list */    
    $list=XWModuleListFactory::getFullModuleList();
		$list->sortByName(); 
        
        $module=null;
        for($i=0;$i<$list->getSize();$i++){
        	$module=$list->getModule($i);
        	
        	$style="";
        	if($module->isHidden()){
        		$style="style=\"color:#AAAAAA;\"";
        	}
        	
        	echo "<tr ".$style.">\n";
        	$instance="";       	
        	
        	if($module->isInstance()){
        		echo "  <td class=\"dataTableTdLeft\">".$module->getName()." ".$module->getVersion()."</td>\n";
        		$instance="instance";
        	}
        	else{
        		echo "  <td class=\"dataTableTdLeft\"><img src=\"images/module.png\" alt=\"instance\"/> ".$module->getName()." ".$module->getVersion()."</td>\n";
        		$instance="global";
        	}
        	 
        	echo "  <td class=\"dataTableTdRight\">pagename alias: <a href=\"index.php?page=".$module->getCallName()."\">".$module->getCallName()."</a></td>\n";
        	echo "  <td class=\"dataTableTdLeft\">".$module->getPath()."</td>\n"; 
        	if($module->isHidden()){
        		echo "  <td class=\"dataTableTdRight\">[<a href=\"index.php?adminpage=1&page=".$request["page"]."&sub=moduleHidde&callName=".$module->getCallName()."&instance=".$instance."\">make visible</a>]</td>\n"; 
        	} 
        	else{
        		echo "  <td class=\"dataTableTdRight\">[<img src=\"images/invisible.png\" alt=\"visible\"/> <a href=\"index.php?adminpage=1&page=".$request["page"]."&sub=moduleHidde&callName=".$module->getCallName()."&instance=".$instance."\">hidde</a>]</td>\n"; 
        	}
        	echo "</tr>\n";
        	
        	if($module->getAdminGroup()!=""){
        		echo "<tr>\n";
        		echo "  <td>&nbsp;</td>\n";
        		echo "  <td class=\"dataTableTdRight\" style=\"font-style:italic;font-size:0.9em;\" colspan=\"3\">\n";
        		echo "Admin-Group: ".$module->getAdminGroup();     		
				echo "</td>\n";
        		echo "</tr>\n";        		
        	}
        	
        	if($module->getSize()>0){
        		echo "<tr>\n";
        		echo "  <td>&nbsp;</td>\n";
        		echo "  <td class=\"dataTableTdRight\" style=\"font-style:italic;font-size:0.9em;\" colspan=\"3\">\n";
        		echo "		User-Groups:<br/>\n";
				echo "      <ul class=\"protected\">\n";
        		for($iP=0;$iP<$module->getSize();$iP++){
        			echo "			<li>".$module->getPermission($iP)."</li>\n";
        		}   
        		echo "		</ul>\n";     		
				echo "</td>\n";
        		echo "</tr>\n";
        	}
        }
    ?>
  </table>
</div>
<?php
}else{
?>

<?php
}
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>
