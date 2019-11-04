<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWGroupList;

/*
 * Created on 20.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
 if(isset($request["newGroupName"]) && $request["newGroupName"]!="" && $_SESSION["XWUSER"]->isInGroup("admins")){
 	$group=new XWGroup();
 	$group->setName($request["newGroupName"]);
 	$group->setDescription($request["newGroupDescription"]);
 	if($_SESSION["XWUSER"]->isInGroup("admins")){
 		$group->save();
 	} 	
 }
 */

$request = XWRequest::instance()->getRequestAsArray();
?>
<div class="ActionBoxHeader">Groups:</div>
<?php
if($_SESSION["XWUSER"]->isInGroup("admins")){

?>
<div class="ActionBox">
  [<a href="index.php?page=<?=$request["page"] ?>&sub=groupEdit&adminpage=1&groupId=0">new group</a>]
</div>
<div class="PresentationBox">
  <table>
    <?php
        $list=new XWGroupList();
        $list->load();
        $group=null;
        for($i=0;$i<$list->getSize();$i++){
            $group=$list->getGroup($i);
            echo "<tr>\n";
            echo "  <td class=\"dataTableTdLeft\"><img src=\"images/group.png\"/> <a href=\"index.php?page=".$request["page"]."&sub=groupUsers&adminpage=1&groupId=".$group->getId()."\">".$group->getName()."</a></td>\n";
            echo "  <td class=\"dataTableTdRight\">".$group->getDescription()."</td>\n";
            if($group->getName()!="admins"){
            	echo "  <td class=\"dataTableTdRight\">[<a href=\"index.php?page=".$request["page"]."&sub=groupEdit&adminpage=1&groupId=".$group->getId()."\">edit</a>]</td>\n";
            	$sec=XWAddonManager::instance()->getAddonByName("XWUserSession")->getURLParameterWithSessionSecToken(); 
            	echo "  <td class=\"dataTableTdRight\">[<a href=\"index.php?page=".$request["page"]."&sub=groupDelete&adminpage=1&".$sec."&groupId=".$group->getId()."\">delete</a>]</td>\n"; 
            }
            else{
            	echo "  <td class=\"dataTableTdRight\">-</td>\n";
            	echo "  <td class=\"dataTableTdRight\">-</td>\n"; 
            }
            
            echo "</tr>\n";	
        }    
    ?>
  </table>
</div>
<?php	
}
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">Administration</a></div>