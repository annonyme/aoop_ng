<?php
use core\addons\XWAddonManager;
use core\utils\filesystem\XWFileList;
use core\utils\filesystem\XWFolderList;
use core\net\XWRequest;
use core\utils\config\GlobalConfig;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 25.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$request = XWRequest::instance()->getRequestAsArray();
?>

<?php
  if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){

      $currentInstance = XWServerInstanceToolKit::instance()->getCurrentInstanceName();
      $root = \core\utils\config\GlobalConfig::instance()->getValue("imagesfolder") . $currentInstance . "/uploads/";
      if(!is_dir($root)){
          mkdir($root, 0777, true);
      }
      ?>
<div class="panel panel-default">
<div class="panel-heading">
	Upload Image:
</div>
<div class="panel-body">
  <form action="index.php?page=<?=$request["page"] ?>&sub=uploadImage&adminpage=1" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <td>Image:</td>
        <td><input type="file" name="upfile" required/></td>
      </tr>
      <tr class="hidden">
        <td>Folder:</td>
        <td>
          <select name="upfolder">
            <option value="">[root]</option>
            <?php              
              $dirs=new XWFolderList();
              $dirs->load($root,true);
              $selected="";
              for($i=0;$i<$dirs->getSize();$i++){
              	$dir=$dirs->getFolder($i);
              	$callPath=preg_replace("/\//i","___",$dir->getPath());
              	$callPath=preg_replace("/images___/","",$callPath);
              	if(isset($request["showFolder"]) && $request["showFolder"]==$callPath){
              		$selected=" selected=\"selected\" ";
              	}
              	else{
              		$selected="";
              	}
              	echo "  <option ".$selected." value=\"".$callPath."\">".preg_replace("/images\//","",$dir->getPath())."</option>\n";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2"><input type="submit" class="btn btn-default" value="upload"/></td>
      </tr>
    </table>
  </form>
</div>
</div>
<br/>
<?php
  }

  if(!isset($request["showFolder"])){
  	  $request["showFolder"]="";
  }
  $realPath=preg_replace("/___/i","/",$request["showFolder"]);
  $realPath=preg_replace("/(\/{2)|(\.{2)/i","",$realPath);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Images:
    </div>
    <div class="panel-body">
      <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=<?=$request["sub"] ?>&adminpage=1"  class="hidden">
        <select name="showFolder" onchange="submit()">
                <option value="">[root]</option>
                <?php
                  $dirs=new XWFolderList();
                  $dirs->load($root,true);
                  $selected="";
                  $dir=null;
                  for($i=0;$i<$dirs->getSize();$i++){
                    $dir=$dirs->getFolder($i);
                    $callPath=preg_replace("/\//i","___",$dir->getPath());
                    $callPath=preg_replace("/images___/","",$callPath);
                    if($request["showFolder"]==$callPath){
                        $selected=" selected=\"selected\" ";
                    }
                    else{
                        $selected="";
                    }
                    echo "  <option ".$selected." value=\"".$callPath."\">".preg_replace("/images\//","",$dir->getPath())."</option>\n";
                  }
                ?>
        </select>
        <input type="submit" class="submit" value="show"/>
      </form>
    </div>
    <table class="table">
    <?php

      $useLightBox=false;
      if(XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->existsInfo("photobook_uselightbox")){
          $useLightBox=XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->getInfoByName("photobook_uselightbox")=="true";
      }
      else{
          if(XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->existsInfo("photobook_usefancybox")){
              $useLightBox=XWAddonManager::instance()->getAddonByName("XWServerInstanceInfos")->getInfoByName("photobook_usefancybox")=="true";
          }
      }

      if($useLightBox){
                    echo "<script type=\"text/javascript\">\n";
                    echo "$(document).ready(function() {\n";
                    echo "  $('a.prevImage').fancybox({";

                    echo "'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {".
                        "return '<span id=\"fancybox-title-over\" class=\"fancyBoxImageCounter\">' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';".
                    "},\n";
                    echo "'transitionIn':'none','transitionOut':'none',\n";
                    echo "'hideOnContentClick':true,\n";
                    echo "'cyclic':true,\n";
                    echo "'type':'image',\n";

                    echo "   });\n";
                    echo "});\n";
                    echo "</script>\n";
      }

      $images=new XWFileList();
      $images->load($root.$realPath);

      $sec=XWAddonManager::instance()->getAddonByName("XWUserSession")->getURLParameterWithSessionSecToken();

      for($i=0;$i<$images->getSize();$i++){
        if(preg_match("/(\.jp(e)?g$)|(\.png$)|(\.bmp$)|(\.gif$)/i",$images->getFile($i))){
            echo "<tr>" .
                 "<td class=\"\">/" . $root . $images->getFile($i) . "</td>" .
                 "<td class=\"\"><a rel=\"prevImg\" class=\"prevImage\" href=\"".$root . $realPath.$images->getFile($i)."\" target=\"_blank\"><img style='max-height:150px;' src='" . $root . $images->getFile($i) . "'></a></td>";
            echo "<td class=\"\">[<a href=\"".$root . $realPath.$images->getFile($i)."\" target=\"_blank\">link/view</a>]</td>\n";

            if($_SESSION["XWUSER"]->isInGroup("admins")){
                echo "<td class=\"\">[<a href=\"index.php?adminpage=1&page=".$request["page"]."&sub=deleteImage&imageName=".$images->getFile($i)."&subdir=".$request["showFolder"]."&".$sec."\">delete</a>]</td>\n ";
            }
            echo "</tr>\n";
        }
      }
    ?>
    </table>
</div>
<?php
  if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("instanceAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){
?>
<br/>
<div class="ActionBoxHeader hidden">
	Create new sub-folder:
</div>
<div class="ActionBox hidden">
  <form method="post" action="index.php?page=<?=$request["page"] ?>&sub=createNewFolder&adminpage=1">
	  <?php
	  		XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
	  ?>
	  <strong>Sub-folder:</strong> <?=$root ?> <input type="text" name="newFolder" required/><br/>
	  <input type="submit" class="submit" value="create"/>
  </form>
</div>
<?php
  }
?>
<br/>
<div class="panel panel-default">
<div class="panel-heading">Back:</div>
<div class="panel-body">back to <a href="index.php?adminpage=1">admin-main</a></div>
</div>