<?php
namespace xw\entities\daos;

use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use PDBC\PDBCObjectMapper;

class XWDAOStub{
    private $db=null;
    
    private static $instance=null;
    
    static public function instance():self{
        if(self::$instance==null){
            self::$instance=new self();
        }
        return self::$instance;
    }
    
    public function __construct(){
        $dbName=XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
        $this->db=PDBCCache::getInstance()->getDB($dbName);
    }
    
    public function load($id, $class){
        $mapper = new PDBCObjectMapper();
        return $mapper->load($this->db, $id, $class);
    }
    
    public function save($entity){
        $mapper = new PDBCObjectMapper();
        return $mapper->merge($this->db, get_class($entity));
    }
    
    public function delete($entity){
        $mapper = new PDBCObjectMapper();
        return $mapper->delete($this->db, get_class($entity));
    }
    
    public function loadList($class):array{
        $mapper = new PDBCObjectMapper();
        return $mapper->loadList($this->db, $class);
    }
}