<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWGroup;
use xw\entities\users\XWUser;
use xw\entities\users\XWUserList;

/*
 * Created on 18.04.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$request = XWRequest::instance()->getRequestAsArray();
 
if($_SESSION["XWUSER"]->isInGroup("admins")){ 
 
if(isset($request["groupId"])){
	$group=new XWGroup();
	$group->load($request["groupId"]);
	
	if(isset($request["addUser"]) && $request["addUser"]>0 && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
		$user=new XWUser();
		$user->load($request["addUser"]);
		$group->saveUserTo($user);
		$group->load($group->getId());
	}
	
	if(isset($request["removeUser"]) && $request["removeUser"]>0 && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
		$user=new XWUser();
		$user->load($request["removeUser"]);
		$group->removeUserFrom($user);
		$group->load($group->getId());
	}
	
	?>
<div class="ActionBoxHeader">Add/Remove to/from <?=$group->getName() ?></div>
<div class="ActionBox">
  <table>
    <tr>
      <td>
        <form action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=<?=$request["sub"] ?>" method="post">
          <?php
		  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
		  ?>
          <input type="hidden" name="groupId" value="<?=$group->getId() ?>"/>
          <strong>All user:</strong><br/>
          <select name="addUser" size="10" style="width:99%">
          <?php
             $list=new XWUserList();
             $list->load();
             $user=null;
              for($i=0;$i<$list->getSize();$i++){
              	  $user=$list->getUser($i);
              	  //update 0.3.5.4
              	  if($group->existsIn($user)){
              	  	  echo "<option value=\"0\" style=\"text-decoration:line-through;\">".$user->getName()."</option>\n";
              	  }
              	  else{
              	  	  echo "<option value=\"".$user->getId()."\">".$user->getName()."</option>\n";
              	  }              	  
              }
          ?>
          </select>
          <br/>
          <input type="submit" class="submit" value="add to <?=$group->getName() ?>"/>
        </form>
      </td>
      <td>
        <form action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=<?=$request["sub"] ?>" method="post">
          <?php
		  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
		  ?>
          <input type="hidden" name="groupId" value="<?=$group->getId() ?>"/>
          <strong>Users in <?=$group->getName() ?>:</strong><br/>
          <select name="removeUser" size="10" style="width:99%">
          <?php
              $user=null;
              for($i=0;$i<$group->getSize();$i++){
              	  $user=$group->getUser($i);
              	  echo "<option value=\"".$user->getId()."\">".$user->getName()."</option>\n";
              }
          ?>
          </select>
          <br/>
          <input type="submit" class="submit" value="remove from <?=$group->getName() ?>"/>
        </form>
      </td>
    </tr>
  </table>
</div>	
<?php
} 
else{
?>
  <div class="WarningBoxHeader">No GroupId</div>
  <div class="WarningBox">
    No group-id found in request.
  </div>
<?php	
}

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
<div class="PresentationBox">back to <a href="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=groups">Groups-Administration</a></div>
