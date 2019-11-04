<?php
use core\addons\XWAddonManager;
use core\net\XWRequest;
use xw\entities\users\XWUser;
use core\utils\XWCodeGenerator;

/*
 * Created on 29.10.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$request = XWRequest::instance()->getRequestAsArray();
if($_SESSION["XWUSER"]->isInGroup("admins") && XWAddonManager::instance()->getAddonByName("XWUserSession")->checkSessionSecToken()){ 
    $user=new XWUser();
    if(isset($request["newUserName"])){
 	    if(isset($request["newUserId"])){
 	    	$user->load($request["newUserId"]);
 	    }
 	    
 	    if(!isset($request["newUserName"]) && isset($request["newUserEmail"]) && filter_var(trim($request["newUserEmail"]), FILTER_VALIDATE_EMAIL)){
 	    	$request["newUserName"] = $request["newUserEmail"];
 	    }
 	    
 	    $user->setName($request["newUserName"]);
 	    $user->setEmail($request["newUserEmail"]);
 	    $user->setActive(true);
 	    
 	    if($request["newUserPassword"]==""){
 	    	$gen=new XWCodeGenerator();
			$password=$gen->generate();
			$user->save($password);
			?>
 	        <div class="PresentationBoxHeader">User created</div>
            <div class="PresentationBox">Password is <strong><?=$password ?></strong> 
            write email to <a href="mailto:<?=$user->getEmail() ?>"><?=$user->getEmail() ?></a>.</div>
 	        <?php
 	    }
 	    else{
 	    	if($request["newUserPassword"]==$request["newUserPassword2"]){
 	    		$user->save($request["newUserPassword"]); 	    		
 	    		?>
 	     		<div class="PresentationBoxHeader">User created:</div>
         		<div class="PresentationBox">User was created.</div>
 	     		<?php
 	    	}
 	    	else{
 	    		?>
 	            <div class="WarningBoxHeader">no equal passwords</div>
                <div class="WarningBox">User wasn't created!</div>
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
<div class="PresentationBox">back to <a href="index.php?page=<?=$request["page"] ?>&sub=usersList&adminpage=1">Users-Administration</a></div>
