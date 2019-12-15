<?php
/*
 * Created on 31.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\addons\XWAddon;
use core\addons\XWAddonManager;
use core\net\XWRequest;
use core\utils\filesystem\XWSubDirList;
use core\utils\config\GlobalConfig;
 

$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins")){ 
	$subDirList=new XWSubDirList();
	$subDirList->findSubDirs(GlobalConfig::instance()->getValue("addonfolder"));
?>
<div class="PresentationBoxHeader">Installed addons</div>
<div class="PresentationBox">
  <table class="table">
    <?php
        for($i=0;$i<$subDirList->getSize();$i++){
        	$img="";
        	if(file_exists("addons/".$subDirList->getSubDir($i)."/config.xml")){
        		$img=" <img src=\"images/configFile.png\" alt=\"configfile\"/>";
        	}
        	
        	echo "<tr>\n";
        	echo "  <td class=\"dataTableTdLeft\">".$subDirList->getSubDir($i)." ".$img."</td>\n";
        	//if($img!=""){
        		echo "  <td class=\"dataTableTdRight\"><a class=\"btn btn-primary btn-sm\" href=\"index.php?adminpage=1&page=".XWRequest::instance()->getString("page")."&sub=".XWRequest::instance()->getString("sub")."&module=".$subDirList->getSubDir($i)."\">view</a></td>\n";
        	//}
        	//else{
        	//	echo "  <td class=\"dataTableTdRight\">&nbsp;</td>\n";
        	//}
        	
        	//echo "  <td>[<a href=\"\">delete</a>]</td>\n";
        	echo "</tr>\n";
        	if(isset($request["module"])){
        		if($request["module"]==$subDirList->getSubDir($i)){
        			if(file_exists("addons/".$subDirList->getSubDir($i)."/".$subDirList->getSubDir($i).".php")){
        				XWAddonManager::instance()->getAddonByName($subDirList->getSubDir($i));
        				$methods=get_class_methods($subDirList->getSubDir($i));
        				for($j=0;$j<count($methods);$j++){
        					echo "<tr>\n";
        				    echo "  <td colspan=\"2\" class=\"dataTableTdRight\">".$methods[$j]."(....)</td>\n";
        				    echo "</tr>\n";
        				}
        				$addon=new XWAddon();
        				$addon->load($request["module"],GlobalConfig::instance()->getValue("addonfolder"));
        				if(isset($request["config"]) && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
        					$addon->setConfig($request["config"]);
        					$addon->save();
        					$addon->load($request["module"],GlobalConfig::instance()->getValue("addonfolder"));
        				}
        				
        				if($addon->getConfig()!=""){
        					$fileName=preg_replace("/^.+\/([^\/]+$)/iu","$1",$addon->getConfigFileName());
        					
        					echo "<tr>\n";
        				    echo "  <td colspan=\"2\" class=\"dataTableTdRight\">\n";
							echo "  <form method=\"post\" action=\"index.php?page=".XWRequest::instance()->getString("page")."&sub=".XWRequest::instance()->getString("sub")."&adminpage=1&module=".$request["module"]."\">\n";

  							XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();

							echo "    <strong>".$fileName.":</strong><br/><textarea style=\"width:99%;height:100px;\" name=\"config\">".$addon->getConfig()."</textarea>\n";
							if($addon->isServerInstanceConfig()){
								echo "    <input type=\"submit\" class=\"submit\" value=\"save (instance)\"/>\n"; 
							}
							else{
								echo "    <input type=\"submit\" class=\"submit\" value=\"save\"/>\n"; 
							}							
							echo "  </form>\n";
							echo "  </td>\n";
        				    echo "</tr>\n";
        				}
        			}
        			else{
        				echo "<tr>\n";
        				echo "  <td colspan=\"2\">no config XML found... </td>\n";
        				echo "</tr>\n";
        			}
        			
        		}
        	}
        }
    ?>
  </table>  
</div>
<?php
}
else{
	?>
 	<div class="WarningBoxHeader">Access denied</div>
    <div class="WarningBox">Admin-Rights needed!</div>
 	<?php
}
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>