<?php
/*
 * Created on 19.12.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if($_SESSION["XWUSER"]->isInGroup("pagesAdmins") || $_SESSION["XWUSER"]->isInGroup("admins")){ 
    
    	?>
 	    <div class="WarningBoxHeader">no filename</div>
        <div class="WarningBox">no filename found!</div>
 	    <?php
 
}
else{
	?>
 	<div class="WarningBoxHeader">Access denied</div>
    <div class="WarningBox">Admin-Rights needed!</div>
 	<?php
}     
?>
