<?php
/*
 * Created on 25.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWUserList;

$request = XWRequest::instance()->getRequestAsArray();
 $userlist=new XWUserList();
 $userlist->load();

if($_SESSION["XWUSER"]->isInGroup("admins")){
	$addonManager=XWAddonManager::instance();
?>
<div class="ActionBoxHeader">
  Create new User (user will be 'active'):
</div>
<div class="ActionBox">
  <form method="post" action="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=createUser">
  	<?php
  		$addonManager->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  	?>
    <table>
      <tr>
        <td class="dataTableTdLeft">Email:</td>
        <td class="dataTableTdRight"><input type="email" name="newUserEmail" required/></td>
      </tr>
      <tr>
        <td class="dataTableTdLeft">Password:</td>
        <td class="dataTableTdRight"><input type="text" name="newUserPassword" required/></td>
      </tr>
      <tr>
        <td class="dataTableTdLeft">reenter Password:</td>
        <td class="dataTableTdRight"><input type="text" name="newUserPassword2" required/></td>
      </tr>      
      <tr>
        <td colspan="2" class="dataTableTdRight"><input type="submit" class="submit" value="create"/></td>
      </tr>
    </table>
  </form>
</div>
<br/>
<div class="PresentationBoxHeader">
    Active Users (<?=$userlist->getSize() ?>):
</div>
<div class="PresentationBox">
<table class="table">
  <?php
  $user=null;
  for($i=0;$i<$userlist->getSize();$i++){
  	$user=$userlist->getUser($i);
  	echo "  <tr>\n";
  	echo "    <td class=\"dataTableTdLeft\">".$user->getId()."</td>\n";
  	echo "    <td class=\"dataTableTdLeft\">".$user->getName()."</td>\n" .
  		 "    <td class=\"dataTableTdRight\">[<a href=\"mailto:".$user->getEmail()."\">".$user->getEmail()."</a>]</td>\n";
  	echo "    <td class=\"dataTableTdRight\">".$user->getRegistrationDate()."</td>\n";
  	
  	$sec=$addonManager->getAddonByName("XWUserSession")->getURLParameterWithSessionSecToken();
  	echo "    <td class=\"dataTableTdLeft\"><a class=\"btn btn-primary\" href=\"index.php?page=".$request["page"]."&sub=activeUser&adminpage=1&state=inactive&sub=activeUser&".$sec."&userId=".$user->getId()."\">set inactive</a></td>\n";
  	echo "    <td class=\"dataTableTdRight\"><a class=\"btn btn-primary\" href=\"index.php?adminpage=1&page=".$request["page"]."&sub=userPasswordEdit&userId=".$user->getId()."\">new password</a></td>\n";
  	echo "  </tr>\n";
  }  
  ?>
</table>
</div>
<br/>
  <?php
  $userlist->load("0");
  ?>
<div class="PresentationBoxHeader">
    InActive Users (<?=$userlist->getSize() ?>):
</div>
<div class="PresentationBox">
<table>
  <?php  
  $user=null;
  for($i=0;$i<$userlist->getSize();$i++){
  	$user=$userlist->getUser($i);
  	echo "  <tr>\n";
  	echo "    <td class=\"dataTableTdLeft\">".$user->getId()."</td>\n";
  	echo "    <td class=\"dataTableTdLeft\">".$user->getName()."</td>\n" .
  		 "    <td class=\"dataTableTdRight\">[<a href=\"mailto:".$user->getEmail()."\">".$user->getEmail()."</a>]</td>\n";
  	echo "    <td class=\"dataTableTdRight\">".$user->getRegistrationDate()."</td>\n";	
  	
  	$sec=$addonManager->getAddonByName("XWUserSession")->getURLParameterWithSessionSecToken(); 
  	echo "    <td class=\"dataTableTdLeft\"><a class=\"btn btn-primary\" href=\"index.php?page=".$request["page"]."&sub=activeUser&adminpage=1&state=active&".$sec."&sub=activeUser&userId=".$user->getId()."\">set active</a></td>\n";
  	echo "    <td class=\"dataTableTdRight\"><a class=\"btn btn-primary\" href=\"index.php?adminpage=1&page=".$request["page"]."&sub=userPasswordEdit&userId=".$user->getId()."\">new password</a></td>\n";
  	echo "  </tr>\n";
  }  
  ?>
</table>
</div>
<br/>
<?php
}
?>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>