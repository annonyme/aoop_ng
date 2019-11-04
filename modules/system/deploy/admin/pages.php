<?php
use core\addons\XWAddonManager;
use core\utils\filesystem\XWFileList;
use core\net\XWRequest;
use core\utils\XWServerInstanceToolKit;

/*
 * Created on 28.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$request = XWRequest::instance()->getRequestAsArray();

$files=new XWFileList();
$pageDir=XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . "pages";
$files->load($pageDir);

if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){ 
?>
	<div class="PresentationBoxHeader">
		File-Browser: <?=$pageDir ?>
	</div>
	<div class="PresentationBox">
		<table class="pagesList">
			<?php
				for($i=0;$i<$files->getSize();$i++){
					$file=$files->getFile($i);
					if(preg_match("/(\.xml)|(\.html)|(\.json)$/i",$file)){
						$callName=preg_replace("/(\.xml)|(\.html)$/i","",$file);
						$type="unkown";
						if(preg_match("/(\.template\.html)$/i",$file)){
							$type="template";
						}
						else if(preg_match("/(\.page\.json)$/i",$file)){
							$type="page";
						}
						else if(preg_match("/(\.html)$/i",$file)){
							$type="page";
						}
						else if(preg_match("/(\.xml)$/i",$file)){
							$type="sidecar";
						}
						
						?>
						<tr>
							<td class="dataTableTdLeft"><?=$file ?></td>
							<td class="dataTableTdRight" style="text-align:right;"><?=filesize($pageDir . "/" . $file) ?> Bytes</td>
							<td class="dataTableTdRight">[<a target="_blank" href="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=pageShowContent&_resource=bypage&type=<?=$type ?>&pageName=<?=$callName ?>">view</a>]</td>
							<td class="dataTableTdRight">[delete]</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
	</div>
	<script type="text/javascript" src="reachableContent/js/generalLib.js"></script>
	<div class="PresentationBox">
		<div id="dropBox">
			Drop here!<br/>
			(one file per time)<br/>
			<span style="display:none;" id="statusField">0%</span>
		</div>
		<?php
  			XWAddonManager::instance()->getAddonByName("XWUserSession")->printHiddenInputWithSessionSecToken();
  		?>
		<script type="text/javascript">
			function handleDragOver(evt) {
				evt.stopPropagation();
				evt.preventDefault();
				evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
			}
			
			function loadFile(evt){
				evt.stopPropagation();
				evt.preventDefault();
				
				var files = null; // FileList object
				files=evt.dataTransfer.files;
				
				//TODO.. matches html or xml
				if(files!=null && files.length>0){
					var filesLength=files.length;
					var file=files[0];					
					
					var props=[];
					try{
						props["_XW_SESSION_SEC"]=document.getElementsByName("_XW_SESSION_SEC")[0].value;
					}
					catch(e){
						
					}
					
					var filename=uploadFile(file,"index.php?adminpage=1&page=system&sub=pageUpload",props,"statusField"); //TODO.. change to uploadFiles
					document.getElementById("statusField").style.display="none";
					window.location.reload();					
				}
				else{
					alert("error!");
				}
			}		
		
			// Setup the dnd listeners.
			var dropZone = document.getElementById('dropBox');
			dropZone.addEventListener('dragover', handleDragOver, false);
			dropZone.addEventListener('drop', loadFile, false);
		</script>
	</div>
<?php
} 
?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>
