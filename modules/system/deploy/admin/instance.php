<?php
use core\addons\XWAddonManager;
use core\utils\filesystem\XWFileList;
use core\utils\XWServerInstanceToolKit;
use core\net\XWRequest;
use xw\entities\users\XWGroupList;
use core\utils\config\GlobalConfig;

/*
 * Created on 17.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
 $urlparts=preg_split("/\//",$_SERVER["PHP_SELF"]);
 $curUrl="";
 for($i=0;$i<count($urlparts)-1;$i++){
     $curUrl.=$urlparts[$i]."/";
 }
 $thisPageURL="http://".$_SERVER["HTTP_HOST"].$curUrl;
 */

$request = XWRequest::instance()->getRequestAsArray();

 $itk=XWServerInstanceToolKit::instance();
 //$switcher=new XWServerSwitch($itk->getCurrentInstanceURLWithParameters()); 
 $switcher=$itk->getServerSwitch();
 if($_SESSION["XWUSER"]->isInGroup("admins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins")){
?>
<div class="ActionBoxHeader">Current instance (<?=$switcher->getName() ?>):</div>
<div class="ActionBox">
<form method="post" action="index.php?page=<?=$request["page"] ?>&sub=saveInstance&adminpage=1">
<?php
  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  	?>
<table>
  <tr>
    <td class="dataTableTdLeft">Template/Theme:</td>
    <td class="dataTableTdRight">
      <select name="template">
          <option selected value="<?=$switcher->getTheme() ?>"><?=$switcher->getTheme() ?></option>
          <option value="<?=$switcher->getTheme() ?>">---------</option>
<?php
  $files=new XWFileList();
  $files->load(GlobalConfig::instance()->getValue("themespath"));
  
    for($i=0;$i<$files->getSize();$i++){
    	if(preg_match("/^.+\.html$/Uis",$files->getFile($i))){
    	    echo "      <option value=\"".$files->getFile($i)."\"> ".$files->getFile($i)."</option>\n";
    	}    	
    }

    $result = \hannespries\events\EventHandler::getInstance()->fireFilterEvent('themes_collect_names', ['names' => []]);
    if(is_array($result['names'])){
        foreach ($result['names'] as $name){
            echo "      <option value=\"" . $name . "\"> " . $name . "</option>\n";
        }
    }
?>        
      </select>
    </td>
  </tr>
  <tr>
    <td class="dataTableTdLeft">Homepage:</td>
    <td class="dataTableTdRight">
      <select name="homepage">
          <option selected value="<?=$switcher->getHomepage() ?>"><?=$switcher->getHomepage() ?></option>
          <option value="<?=$switcher->getHomepage() ?>">---------</option>
<?php
  $files=new XWFileList();
  $files->load($switcher->getPages());
  
    for($i=0;$i<$files->getSize();$i++){
    	if(preg_match("/^.+\.html$/Uis",$files->getFile($i))){
    	    $pagename=preg_replace("/\.html/","",$files->getFile($i));
    	    if($pagename==$switcher->getHomepage()){
    	    	echo "      <option selected value=\"".$pagename."\"> ".$pagename."</option>\n";
    	    }
    	    else{
    	    	echo "      <option value=\"".$pagename."\"> ".$pagename."</option>\n";
    	    }    	    
    	}    	
    }
?>       
      </select>
    </td>
  </tr> 
  <?php
  if($_SESSION["XWUSER"]->isInGroup("admins")){
  ?>
  <tr>
    <td class="dataTableTdLeft">Pages-folder(ends with '/'):</td>
    <td class="dataTableTdRight"><input type="text" name="pages" value="<?=$switcher->getPages() ?>"/></td>
  </tr> 
  <tr>
    <td class="dataTableTdLeft">Addons (ends with '/'):</td>
    <td class="dataTableTdRight"><input type="text" name="addons" value="<?=$switcher->getAddons() ?>"/></td>
  </tr> 
  <tr>
    <td class="dataTableTdLeft">Admin-Group of this instance*:</td>
    <td class="dataTableTdRight"><select name="admins">
          <option selected value="<?=$switcher->getAdmins() ?>"><?=$switcher->getAdmins() ?></option>
          <option value="<?=$switcher->getAdmins() ?>">---------</option>
          <?php
            $groups=new XWGroupList();
            $groups->load();
            $group=null;
            for($i=0;$i<$groups->getSize();$i++){
            	$group=$groups->getGroup($i);
            	echo "<option value=\"".$group->getName()."\">".$group->getName()."</option>\n";
            }
          ?>
        </select>
    </td>
  </tr> 
  <?php
  }
  ?>  
  <tr>
    <td class="dataTableTdLeft">Meta-Keywords (, separeted):</td>
    <td class="dataTableTdRight"><input type="text" name="keywords" value="<?=$switcher->getKeywords() ?>"/></td>
  </tr>  
</table>
<br/>
<input type="submit" class="submit" value="save"/>
</form>
<strong style="color:red;">* 'admins' (Global-Admins) is always Admin-Group. For all instances.
Set to 'admins' if only Global-Admins should have the rights to edit this instance.
If an other group also should have admin rights, select this group.</strong>
</div>
<br/>
<?php
}
?>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>