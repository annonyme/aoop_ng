<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWGroup;

/*
 * Created on 17.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray(); 
if(isset($request["groupId"]) && $_SESSION["XWUSER"]->isInGroup("admins")){
   $group=new XWGroup();
   $group->load($request["groupId"]);
?>
<div class="ActionBoxHeader">Edit group '<?=$group->getName() ?>'</div>
<div class="ActionBox">
  <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=groupSave&adminpage=1">
    <input type="hidden" name="groupId" value="<?=$group->getId() ?>"/>
    <?php
  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  	?>
    <table>
      <tr>
        <td class="dataTableTdLeft">Name:</td>
        <td class="dataTableTdRight"><input type="Text" name="groupName" value="<?=$group->getName() ?>" required/></td>
      </tr>
      <tr>
        <td class="dataTableTdLeft">Description:</td>
        <td class="dataTableTdRight"><input type="Text" name="groupDescription" value="<?=$group->getDescription() ?>"/></td>
      </tr>
      <tr>
        <td class="dataTableTdLeft" colspan="2"><input type="submit" class="submit" value="create"/></td>
      </tr>
    </table>    
  </form>
</div>
<?php
}
else{
	
}
?>
