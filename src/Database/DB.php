<?php
namespace App\Database;

class DB {
    private $host = "sql311.infinityfree.com";
    private $username = "if0_36097223";
    private $password = "XZiiKPxuNDpWDM";

    public function connection() {
        try {
            $sql = "mysql:host=$this->host;dbname=if0_36097223_product_inventory";
            $conn = new \PDO($sql, $this->username, $this->password);
        
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
            
        } catch(\PDOException $err) {
            return $err;
        }
    }
}