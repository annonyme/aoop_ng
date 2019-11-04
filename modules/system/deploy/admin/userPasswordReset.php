<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWUser;
use core\utils\XWCodeGenerator;

/*
 * Created on 03.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){
	if(isset($request["userId"])){
		$user=new XWUser();
		$user->load($request["userId"]);
		if($request["newPassword"]==""){
			$gen=new XWCodeGenerator();
			$password=$gen->generate();
			$user->changePasswordByAdmin($password,$_SESSION["XWUSER"]);
			?>
 	        <div class="PresentationBoxHeader">Password changed</div>
            <div class="PresentationBox">changed to <strong><?=$password ?></strong> 
            write email to <a href="mailto:<?=$user->getEmail() ?>"><?=$user->getEmail() ?></a>.</div>
 	        <?php
		}
		else{
			if($request["newPassword"]==$request["newPassword2"]){
				$user->changePasswordByAdmin($request["newPassword"],$_SESSION["XWUSER"]);
				?>
 	            <div class="PresentationBoxHeader">Password changed</div>
                <div class="PresentationBox">changed to <strong><?=$request["newPassword"] ?></strong> 
                write email to <a href="mailto:<?=$user->getEmail() ?>"><?=$user->getEmail() ?></a>.</div>
 	            <?php
			}
			else{
				?>
 	            <div class="WarningBoxHeader">no equal passwords</div>
                <div class="WarningBox">no equal passwords found!</div>
 	            <?php
			}
		}
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
<div class="PresentationBox">back to <a href="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=usersList">users administration</a></div>
