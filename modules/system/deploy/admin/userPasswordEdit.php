<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWUser;

/*
 * Created on 03.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins")){
	if(isset($request["userId"])){
	   $user=new XWUser();
	   $user->load($request["userId"]);
	?>
	<div class="ActionBoxHeader">
	   Change password of <?=$user->getName() ?> [UserId: <?=$user->getId() ?>]
	</div>
	<div class="ActionBox">
	  <form method="post" action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=userPasswordReset">
	  	<?php
  			XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  		?>
	    <input type="hidden" name="userId" value="<?=$user->getId() ?>"/>
	    <table>
	      <tr>
	        <td>new password:</td>
	        <td><input type="text" name="newPassword"/></td>
	      </tr>
	      <tr>
	        <td>reenter new password:</td>
	        <td><input type="text" name="newPassword2"/></td>
	      </tr>
	      <tr>
	        <td><input type="submit" class="submit" value="save"/></td>
	      </tr>
	    </table>
	  </form>
	</div>
	<?php
	}
	else{
		?>
 	    <div class="WarningBoxHeader">no user id</div>
        <div class="WarningBox">no user id found!</div>
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
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>