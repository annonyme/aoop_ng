<?php
/*
 * Created on 21.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\addons\XWAddonManager;
use core\utils\filesystem\XWFileList;
use core\database\XWSearchStringParser;
use core\net\XWRequest;
use core\pages\plain\XWPage;
use xw\entities\users\XWUser;
use core\utils\XWServerInstanceToolKit;
use core\utils\config\GlobalConfig;
 
$request = XWRequest::instance()->getRequestAsArray();
$files=new XWFileList();
$addonManager=XWAddonManager::instance();
$pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
$files->load($pageDir);
 
$locale="";
$security=new XWSearchStringParser();
if(isset($request["locale"])){
    $locale=$security->simpleStringCleaning($request["locale"]);
} 

if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){ 
?>
<div class="panel panel-default">
    <div class="panel-heading">
      Page-folder [<?=$pageDir ?>] of instance '<?=$addonManager->getAddonByName("XWServerInstanceInfos")->getInstanceName() ?>':
    </div>

      <table class="table">
        <?php
        $wysiwygEditor = GlobalConfig::instance()->getValue('wysiwygEditor', false);
       
        
        for($i=0;$i<$files->getSize();$i++){
        	if(preg_match("/\.htm(l)?$/",$files->getFile($i)) || preg_match("/\.bkup$/",$files->getFile($i))){	    	
    	    	$callName=preg_replace("/\.htm(l)?$/Uis","",$files->getFile($i));
    	    	$page=new XWPage();
    	    	$page->load($callName,$pageDir);
    	    	
    	    	$pageLocale=new XWPage();
    	    	$pageLocale->load($callName,$pageDir,$locale);
    	    	
    	    	if($page->getName()!="" && ($locale=="" || $page->getPath()!=$pageLocale->getPath())){
    	    		
    		    	$addLink="";
    		    	$img="";
    		    	
    		    	echo "    <tr>\n"; 
    		    	   	
    		    	if(preg_match("/(\.htm|bkup)$/Uis",$files->getFile($i))){
    		    		 
    		    	}
    		    	else{		    		
    		    		$lastUser=new XWUser();
    		    		$lastUser->load($page->getUserId());
    		    		
    		    		if($page->isHidden()){
    		    			echo "      <td>".$img." <a class=\"btn btn-primary\" href=\"index.php?page=".$page->getCallName()."\" target='_blank'>View</a></td>";
    		    		}
    		    		else{
    		    			echo "      <td>".$img." <a class=\"btn btn-primary\" href=\"index.php?page=".$page->getCallName()."\" target='_blank'>View</a></td>";
    		    				 
    		    		}
    		    		echo "      <td><strong>".$page->getCallName()."</strong></td>\n";
    		            echo "      <td>".$pageLocale->getName()."</td>\n";
    		    		if($page->getParentPage()!=""){
    		    			echo "      <td><i class=' glyphicon glyphicon-circle-arrow-down '></i> ".$page->getParentPage()."</td>\n";
    		    		}
    		    		else{
    		    			echo "      <td>&nbsp;</td>\n";
    		    		}
    		    				    		
    		    		
    		    		
    		    		//Tiny MCE Editor?    
    		    		if($wysiwygEditor){
    		    			echo "<td> Edit: ";
    		    			echo "<a class=\"btn btn-primary\" href=\"index.php?page=".$request["page"]."&sub=editPage&adminpage=1&pageName=".$callName."\">HTML-Code</a> " .
    		    		         "<a class=\"btn btn-primary btn-sm\" href=\"index.php?page=".$request["page"]."&sub=editPage&adminpage=1&editor=1&pageName=".$callName."\"><small>WYSIWYG</small></a> ".$addLink."";
    		                echo "</td>\n";
    		    		}
    		    		else{
    		    			echo "<td>";
    		    			echo "<a href=\"index.php?page=".$request["page"]."&sub=editPage&adminpage=1&pageName=".$callName."\">[edit]</a> ".$addLink."";
    		                echo "</td>\n";
    		    		}
    		    		//     "[<a href=\"index.php?page=renameFile&adminpage=1&pageName=".$files->getFile($i)."\">rename</a>] ";
    		    		if($_SESSION["XWUSER"]->isInGroup("admins")) {
    		    		    echo "<td>";
    		    		    echo "<a class=\"btn btn-primary\" href=\"index.php?page=".$request["page"]."&sub=deleteFile&adminpage=1&pageName=".$callName."\">delete</a>";
    		    		    echo "</td>\n"; 	
    		    		}
    		    		else{
    		    			echo "<td>&nbsp;</td>\n";
    		    		}  	
    		    		echo "      <td>(edited by: ".$lastUser->getName().")</td>\n";
    		    	}
    		    	echo "    </tr>\n";
    	    	}	    	    	
        	}
        }   
        ?>
      </table>

</div>
<br/>
<div class="panel panel-default">
    <div class="panel-heading">Create new page:</div>
    <div class="panel-body">
      <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=createNewPage&adminpage=1">
      	<?php
      		$addonManager->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
      	?>
        <table class="table">
          <tr>
            <th>Title:</th>
            <td><input class="form-control" type="text" name="newPage" required/></td>
          </tr>
          <tr>
            <th>Callname:</th>
            <td><input class="form-control" type="text" name="newPageCallname" required/></td>
          </tr>
          <tr>
            <th>Parent-Page:</th>
            <td>
              <select name="newPageParent">
                   <option value="">-</option>
                 <?php
    			 for($i=0;$i<$files->getSize();$i++){
    			 	if(preg_match("/\.html$/Uis",$files->getFile($i))){
    			 		$selectionPage=new XWPage();
    			 		$selectionPage->load(preg_replace("/\.html$/Uis","",$files->getFile($i)),$pageDir);
    			 		if($selectionPage->getName()!=""){
    			 			echo "<option value=\"".$selectionPage->getCallName()."\">".$selectionPage->getName()." (".$selectionPage->getCallName().")</option>\n";	
    			 		}			 					 		
    			 	}
    			 }             
                 ?>
                 </select>
            </td>
          </tr>
        </table>  
        <input type="submit" class="btn btn-default" value="create"/>
      </form>
    </div>
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
<div class="panel panel-default">
<div class="panel-heading">Back:</div>
<div class="panel-body">back to <a href="index.php?adminpage=1">admin-main</a></div>
</div>
