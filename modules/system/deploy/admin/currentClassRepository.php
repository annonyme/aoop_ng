<?php
/*
 * Created on 27.06.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<div class="InfoBoxHeader">Current loaded classes:</div>
<div class="InfoBox">
  <table>
  <?php
  $cache=$_SESSION["XWCLASSCACHE"];
  $i=0;
  foreach($cache as $key => $value){
  	echo "<tr>\n";
  	echo "  <td class=\"dataTableTdLeft\">".++$i."</td>\n";
  	echo "  <td class=\"dataTableTdRight\">".$key."</td>\n";
  	echo "  <td class=\"dataTableTdRight\">".$value."</td>\n";
  	echo "</tr>\n";
  }
  ?>
  </table>
</div>
<div class="InfoBox">
	<table>
		<tr>
			<td class="dataTableTdLeft">PHP-Version:</td>
			<td class="dataTableTdRight"><?=phpversion() ?></td>
		</tr>
	</table>
</div>
<br/>
<div class="PresentationBoxHeader">Back:</div>
<div class="PresentationBox">back to <a href="index.php?adminpage=1">admin-main</a></div>
