<?php
/*
 * Created on 12.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use core\utils\filesystem\XWFileList;
use core\utils\filesystem\XWFolderList;
use core\net\XWRequest;


$request = XWRequest::instance()->getRequestAsArray();
 //update der dateien (hochladen.. und �berschreiben.. f�r die n�chste version..)
?>
<div class="PresentationBoxHeader">Themes:</div>
<br/>
<?php 
	$dirs=new XWFolderList();
	$dirs->load("themes");
	
	for($i=0;$i<$dirs->getSize();$i++){
		$dir=$dirs->getFolder($i);
		?>
		<div class="PresentationBoxHeader"><?=$dir->getName() ?>:</div>
		<div class="PresentationBox">
			<table>
				<?php 
				$files=new XWFileList();
				$files->load("themes/".$dir->getName()."/");
				for($j=0;$j<$files->getSize();$j++){
					$file=$files->getFile($j);
					?>
					<tr>
						<td class="dataTableTdRight"><?=$file ?></td>
						<td class="dataTableTdLeft">[<a href="index.php?adminpage=1&page=<?=$request["page"] ?>&sub=editTheme&theme=<?=$dir->getName() ?>&fileName=<?=$file ?>">edit</a>]</td>
					</tr>
					<?php 
				}	
				?>
			</table>
		</div>
		<br/>
		<?php
	}

?>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>
