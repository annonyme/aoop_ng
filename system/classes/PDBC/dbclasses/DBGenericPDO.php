<?php
namespace PDBC\dbclasses;

use PDBC\PDBCSQLSecure;
use PDO;
use PDOException;
use Exception;

abstract class DBGenericPDO implements DBInterface {
    private $host = "";
    private $user = "";
    private $password = "";
    
    private $result = array ();
    private $rsetRowCount = 0;
    private $lastException = null;
    
    /**
     * @var PDO
     */
    private $conn = null;
    
    protected abstract function hostDescriptorBuilder(string $ip, int $port = 0, string $serviceName = 'default');
    
    private function secureSQL(string $sql){
        $secure=new PDBCSQLSecure();
        $sql=$secure->removeSemicolonsFromNonStringParts($sql);
        return $sql;
    }
    
    public function __construct(string $url, string $user, string $password) {
        $this->user = $user;
        $this->password = $password;
        $urlTokens = preg_split("/:/", $url);
        
        $this->host = $this->hostDescriptorBuilder($urlTokens[3], (int) $urlTokens[4], $urlTokens[5]);
        $this->conn = new PDO($this->host, $this->user, $this->password);
    }

    public function getNativeConnection(): ?\PDO {
        return $this->conn;
    }
    
    /**
     * execute query an saving resultset.
     */
    public function executeQuery(string $sql) {
        try {
            $sql=$this->secureSQL($sql);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $this->result=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->rsetRowCount = count($this->result);
        } catch (PDOException $e) {
            $this->lastException = $e;
        }
    }
    
    public function getCount():int {
        return $this->rsetRowCount;
    }
    
    public function getResult(int $row = 0, string $column) {
        return $this->result[$row][$column];
    }
    
    public function getRow(int $row = 0) {
        return $this->result[$row];
    }
    
    public function getColumnNames(){
        $list=[];
        if(isset($this->result[0])){
            $list = array_keys($this->result[0]);
        }
        return $list;
    }
    
    /**
     * execute statement
     */
    public function execute(string $sql):int {
        $result=-1;
        try {
            $sql=$this->secureSQL($sql);
            $dbh = $this->conn;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $result=true;
            if(preg_match("/^INSERT/i",$sql)){
                $result=$dbh->lastInsertId();
            }
            
            $this->rsetRowCount=$stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->lastException = $e;
        }
        return $result;
    }
    
    public function executeInsert(string $sql):int {
        return $this->execute($sql);
    }
    
    /**
     * Alias f�r execute().
     */
    public function executeUpdate(string $sql) {
        $this->execute($sql);
    }
    
    public function getLastException():Exception {
        return $this->lastException;
    }
    
    public function beginTransaction() {
        $this->conn->beginTransaction();
    }
    
    public function commit() {
        $this->conn->commit();
    }
    
    public function rollback() {
        $this->conn->rollBack();
    }
}