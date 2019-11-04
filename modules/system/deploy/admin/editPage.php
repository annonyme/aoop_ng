<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/codemirror.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/codemirror.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/mode/xml/xml.minjs"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.47.0/mode/htmlmixed/htmlmixed.min.js"></script>
<style type="text/css">
	.CodeMirror{
		width:99%;
		max-width:700px;
		border:1px solid #000000; 
	}
</style>
<?php
/*
 * Created on 21.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\addons\XWAddonManager;
use core\utils\config\GlobalConfig;
use core\utils\filesystem\XWFileList;
use core\net\XWRequest;
use core\pages\plain\XWPage;
use core\utils\dates\XWCalendar;
use xw\entities\users\XWUser;
use xw\entities\users\XWGroupList;
use core\utils\XWServerInstanceToolKit;

$request = XWRequest::instance()->getRequestAsArray();

if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){ 
 	$addonManager=XWAddonManager::instance();
	$pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
 	if(isset($request["pageName"])){
	    $pageCallName=$request["pageName"];
	    
	    $locale="";
	    if(isset($request["locale"])){
	     	$locale=$request["locale"];
	    }
	    
	    if(isset($request["fileContent"]) && $addonManager->getAddonByName("XWUserSession")->checkSessionSecToken()){	     	
	     	$newContent=stripslashes($request["fileContent"]);	     	
	     	
	     	$savePage=new XWPage();
	     	$savePage->load($request["pageName"],$pageDir,$locale);
	     	
	     	
	     	//parse content and replace placeholders
	     	$values = [];
	     	foreach (XWRequest::instance()->getRequestAsArray() as $vName => $value){
	     	    if(preg_match("/^tempplaceholder_/i", $vName)){
	     	        $realVName = preg_replace("/^tempplaceholder_/i", "", $vName);
	     	        //$newContent = preg_replace("/\{\{".$realVName."\}\}/", stripslashes($value), $newContent);
	     	        $values[$realVName] = htmlspecialchars(stripslashes($value));
	     	    }
	     	}
	     	$savePage->setValues($values);
	     	
	     	
	     	$savePage->setContent($newContent);
	     	$savePage->setHidden(false);
	     	if(isset($request["pageInvisible"])){
	     		if($request["pageInvisible"]=="true"){
	     			$savePage->setHidden(true);
	     		}
	     	}
	     	if(isset($request["pageParent"])){
	     		$savePage->setParentPage($request["pageParent"]);
	     	}
	     	if(isset($request["pageLink"])){
	     	    $savePage->setLink($request["pageLink"]);
	     	}
	     	if(isset($request["pageRestriction"])){
	     		$savePage->setRestriction($request["pageRestriction"]);
	     	}
	     	if(isset($request["pageTitle"])){
	     		$savePage->setName($request["pageTitle"]);
	     	}
	     	if(isset($request["pageDescription"])){
	     		$savePage->setMetaDescription($request["pageDescription"]);
	     	}
	     	$savePage->setUserId($_SESSION["XWUSER"]->getId());
	     	
	     	$savePage->save();
	     	
	     	$cal=new XWCalendar();
	        ?>
	        <div class="panel panel-default">
	        	<div class="panel-heading">Saved page [<a href="index.php?page=<?=$pageCallName ?>" target="blank">view</a>]:</div>
	        	<div class="panel-body">changes to <strong><?=$request["pageName"] ?></strong> was saved (<?=$cal->getMySQLDateString() ?>).</div>
	        </div>
	        <br/>
	        <?php
	    }     
	    
	    $page=new XWPage();
	    $page->load($pageCallName,$pageDir,$locale);	     
	    ?>
	    <div class="panel panel-default">
            <div class="panel-heading">Edit Page: [<?=$page->getCallName() ?>: <?=$page->getName() ?>] <span class="hidden">-- use {{name}} for placeholders</span></div>
            <?php
              if($page->getUserId()>0){
              	$lastEditUser=new XWUser();
              	$lastEditUser->load($page->getUserId());
              	?>
              	<div class="panel-body">Latest version by <strong><?=$lastEditUser->getName() ?></strong></div>
              	<?php
              }
            ?>
            <div class="panel-body">
               <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=editPage&adminpage=1&pageName=<?=$page->getCallName() ?>">
                 <?php
      				$addonManager->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
      			 ?>
                 <table class="table">             	
                 	<tr>
                 	  <td colspan="2">
                 	  	<div class="row">
                            <div class="col-md-8">
                                <textarea name="fileContent" id="fileContent" style="width:100%" rows="30"><?=$page->getContent() ?></textarea>
                            </div>
                            <div class="col-md-4" style="max-height:400px; overflow-y: auto">
                                <div class="row">
                                <?php
                                $currentInstance = XWServerInstanceToolKit::instance()->getCurrentInstanceName();
                                $root = \core\utils\config\GlobalConfig::instance()->getValue("imagesfolder") . $currentInstance . "/uploads/";
                                if(!is_dir($root)){
                                    mkdir($root, 0777, true);
                                }

                                if(is_dir($root)){
                                    $dir = new DirectoryIterator($root);
                                    foreach ($dir as $file){
                                        if(!$file->isDot() && $file->isFile()){
                                            if(preg_match("/((\.jpg)|(\.png)|(\.jpeg)|(\.gif))$/i", $file->getFilename())){
                                                ?>
                                                <div class="col-md-4">
                                                    <img class="img-responsive add-img" src="<?=$root . $file->getFilename() ?>" role="button"/>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                 	  </td>
                 	</tr>
                 	<tr>
                 		<td>Title:</td>
                 		<td><input type="text" name="pageTitle" value="<?=$page->getName() ?>"/></td>
                 	</tr>
                 	<tr>
                 		<td>Menu-Link (URL):</td>
                 		<td><input type="text" name="pageLink" value="<?=$page->getLink() ?>"/></td>
                 	</tr>
                   <tr>
                     <td>Show in menu:</td>
                     <td>
                       <select name="pageInvisible">
                   		<?php
                       if($page->isHidden()){
                       	  ?>
                       	  <option value="false">yes</option>
                   		  <option value="true" selected="selected">no</option>
                       	  <?php
                       }
                       else{
                       	  ?>
                       	  <option value="false" selected="selected">yes</option>
                   		  <option value="true">no</option>
                       	  <?php
                       }
                   		?>
                 		</select>
                     </td>
                   </tr>
                   <tr>
                     <td>Parent-Page:</td>
                     <td>
                       <select name="pageParent">
    		               <option value="">-</option>
                 <?php
                 $files=new XWFileList();
    			 $files->load($pageDir);			 
    			 
    			 for($i=0;$i<$files->getSize();$i++){
    			 	if(preg_match("/\.html$/Uis",$files->getFile($i))){
    			 		$selectionPage=new XWPage();
    			 		$selectionPage->load(preg_replace("/\.html$/Uis","",$files->getFile($i)),$pageDir);
    			 		if($selectionPage->getCallName()!="" && $selectionPage->getCallName() != $pageCallName){
    			 			if($page->getParentPage()==$selectionPage->getCallName()){
    			 				echo "<option value=\"".$selectionPage->getCallName()."\" selected>".$selectionPage->getName()." (".$selectionPage->getCallName().")</option>\n";
    			 			}
    			 			else{
    			 				echo "<option value=\"".$selectionPage->getCallName()."\">".$selectionPage->getName()." (".$selectionPage->getCallName().")</option>\n";
    			 			}	
    			 		}			 					 		
    			 	}
    			 }
                 
                 ?>
    	               </select>
                     </td>
                   </tr>
                   <tr class="hidden">
                     <td>Accessable for:</td>
                     <td>
                       <select name="pageRestriction">
    		               <option value="">all</option>
                 <?php
                 $groups=new XWGroupList();
                 $groups->load();
                 for($i=0;$i<$groups->getSize();$i++){
                 	$selected="";
                 	$group=$groups->getGroup($i);
                 	if($group->getName()==$page->getRestriction()){
                 		$selected="selected";	
                 	}	
                 	?>
                 	<option value="<?=$group->getName() ?>" <?=$selected ?>><?=$group->getName() ?></option>
                 	<?php
                 }             
                 ?>
    	               </select>
                     </td>
                   </tr>
                   <tr>
                 		<td>Meta-Description:</td>
                       <td><textarea maxlength="120" style="width: 100%" name="pageDescription"><?=$page->getMetaDescription() ?></textarea></td>
                 	</tr>
                 </table>
                 <hr>


                 <?php 
                 $placeholders = [];
                 preg_match("/\{\{[a-zA-Z0-9_-]+\}\}/", $page->getContent(), $placeholders);
                 if(count($placeholders) > 0) {
                 ?>
                 Placeholders:
                 <table class="table">
                 <?php
                 foreach($placeholders as $placeholder){
                     $realName = preg_replace("/[\{\}]+/i", "", $placeholder);
                     $existingValue = "";
                     if(isset($page->getValues()[$realName])){
                         $existingValue = $page->getValues()[$realName];
                     }    
                     ?>
                     <tr>
                     	<th><?=$realName ?></th>
                     	<td>
                     		<input type="text" name="tempplaceholder_<?=$realName ?>" value="<?=$existingValue ?>"/>
                     	</td>
                     </tr>
                     <?php 
                 }                 
                 ?>
                 </table>
                 <?php } ?>
                 <input type="submit" class="btn btn-default" value="Save Page"/>
               </form>       
            <?php
    	      if(isset($request["editor"]) && GlobalConfig::instance()->getValue('wysiwygEditor', false)){
                ?>
                  <script src="https://tinymce.cachefly.net/4.0/tinymce.min.js"></script>
                    <script type="text/javascript">
                    tinyMCE.init({
                        selector: '#fileContent',
                        images_upload_url: "index.php?page=<?=$request['page'] ?>&sub=imageUpload&adminpage=1",
                        automatic_uploads: true,
                        toolbar: "image",
                        image_list: "index.php?page=<?=$request['page'] ?>&sub=imageList&adminpage=1"
                    });


                    var func = function(event){
                        if(document.getElementById("fileContent") && tinyMCE){
                            var ed = tinyMCE.get("fileContent");                   // get editor instance
                            var range = ed.selection.getRng();                  // get range
                            var newNode = ed.getDoc().createElement("img");     // create img node
                            newNode.src= event.target.src;        // add src attribute
                            range.insertNode(newNode);                          // insert Node
                        }
                    };

                    var elements = document.querySelectorAll(".add-img");
                    for(var i=0;i<elements.length;i++){
                        var element=elements[i];
                        element.addEventListener("click", func);
                    }

                    </script>
                <?php
    	      }
    	      else{
    	      	?>	      	
    	      	<script type="text/javascript">
    	      		var myTextArea=document.getElementById("fileContent");
    	      		var myCodeMirror = CodeMirror.fromTextArea(myTextArea,{tabSize: 4,
    				      indentUnit: 4,
    				      indentWithTabs: true,
    				      lineWrapping:true,
    				      lineNumbers: true,
    				      lineWrapping: true
    				      });

    	      		var func = function(event){
                        var code = '<img class="img-responsive" src="' + event.target.src + '"/>';
    	      		    if(myCodeMirror){
                            myCodeMirror.replaceSelection(code);
                        }
                    };

                    var elements = document.querySelectorAll(".add-img");
    	      		for(var i=0;i<elements.length;i++){
    	      		    var element=elements[i];
    	      		    element.addEventListener("click", func);
                    }
                    myTextArea.focus();
    	      	</script>
    	      	<?php 
    	      }	
    	    ?>
                    
            </div>
        </div>
        <br/>
        <div class="panel panel-default">
        <div class="panel-heading">Back:</div>
        <div class="panel-body">back to <a href="index.php?page=<?=$request["page"] ?>&sub=pagesList&adminpage=1">Pages-Administration</a> or <a href="index.php?page=<?=$page->getCallName() ?>">view Page</a></div>
        </div>
 	    <?php
    }
    else{
     	 ?>
 	     <div class="WarningBoxHeader">no filename</div>
         <div class="WarningBox">no filename found!</div>
 	     <?php
    } 
}
else{
	?>
 	<div class="WarningBoxHeader">Access denied</div>
    <div class="WarningBox">Admin-Rights needed!</div>
 	<?php
} 
