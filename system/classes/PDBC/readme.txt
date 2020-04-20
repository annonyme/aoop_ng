PDBC_V1.5 (2015/10/09)
======================

Unterstützte Datenbanken:
-------------------------

PDO:

- Oracle
- MySQL
- Firebird
- Generic


PHP:
----

PHP ab Version 7.


Verwendung (Beispiel):
----------------------

	include_once("system/PDBC/PDBCDBFactory.php");
	PDBCDBFactory::init("pdbc/dbclasses/","pdbc/conffiles/");
	$db=PDBCCache::getInstance()->getDB("testdb");

	$sql="SELECT CURRENT_DATE NOW FROM DUAL"; //oracle...
	$db->executeQuery($sql);
	for($i=0;$i<$db->getCount();$i++){
	    echo $db->getResult($i,"NOW")."\n";
	}
	
oder:

	include_once("system/PDBC/PDBCDBFactory.php");
	$pdbcConfFolder="system/PDBC/conffiles/";
	if(is_dir("system/config/pdbc/")){
		$pdbcConfFolder="system/config/pdbc/";
	}
	PDBCDBFactory::init("system/PDBC/dbclasses/",$pdbcConfFolder);	
	
	class Test{
		/**
		 * @dbcolumn=test_id
		 */
		private $id=0;
		/**
		 * @dbcolumn=test_name
		 */
		private $name="";
		
		public function __construct(){
			
		}
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id=$id;
		}
		
		public function getName(){
			return $this->name;
		}
		
		public function setName($name){
			$this->name=$name;
		}
	}
	
	$db=PDBCCache::getInstance()->getDB("embdoop");
	$sql="SELECT test_id,test_name FROM tests ORDER BY test_name ASC";
	$mapper=new PDBCObjectMapper();
	$tests=$mapper->queryList($db, $sql, "Test");
	foreach($tests as $test){
		echo $test->getId()." - ".$test->getName()."<br>\n";
	}	