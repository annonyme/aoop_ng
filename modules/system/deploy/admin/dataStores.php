<?php
use core\datastorage\XWDataStorageFactory;
use core\utils\config\GlobalConfig;

/*
 * Created on 29.10.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<div class="panel panel-default">

<div class="panel-heading">
    Existing DataStores:
</div>
<?php
    $factory=new XWDataStorageFactory(GlobalConfig::instance()->getValue("configspath")."datastorage.xml");    
?>
  <table class="table">
    <tr>
      <td class=""><strong>#</strong></td>
      <td class=""><strong>Name</strong></td>
      <td class=""><strong>Type</strong></td>
      <td class=""><strong>Savepath</strong></td>
      <td class=""><strong>Loadpath (full)</strong></td>
    </tr>
    <?php
    $store=null;
    for($i=0;$i<$factory->getSize();$i++){
    	$store=$factory->getDataStore($i);
    	echo "<tr>\n";
    	echo "  <td><strong>".($i+1)."</strong></td>\n";
    	echo "  <td>".$store->getName()."</td>\n";
    	if($store->getType()=="ftp"){
    		echo "  <td>".$store->getType()."</td>\n";
    	}
    	else{
    		echo "  <td>".$store->getType()."</td>\n";
    	}
    	
    	if($store->getType()=="ftp"){
    		echo "  <td>".$store->getHost()."/".$store->getSavePath()."</td>\n";
    	}
    	else{
    		echo "  <td>".$store->getSavePath()."</td>\n";
    	}
    	echo "  <td>".$store->getLoadFullPath()."</td>\n";
    	echo "</tr>\n";
    }
    ?>
  </table>
  <div class="panel-footer">
  * to add/change/delete DataStores please edit the datastorage.xml in userdata/config
  in your aoop folder.
    </div>
</div>
<br/>
<div class="panel panel-default">
<div class="panel-heading">Back:</div>
<div class="panel-body">back to <a href="index.php?adminpage=1">admin-main</a></div>
</div>
