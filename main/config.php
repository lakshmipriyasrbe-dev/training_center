<?php
    class Database {

        private $host = "localhost";
        private $dbname = "training_center_db";
        private $username = "root";
        private $password = "";
        
        public $conn;

        public function __construct() {
            $this->connect();
        }

        public function connect() {
            try {
                $this->conn = new PDO(
                    "mysql:host=".$this->host.";dbname=".$this->dbname,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("SET NAMES utf8");
                return $this->conn;
            }
            catch(PDOException $e) {
                echo "Database Connection Failed : ".$e->getMessage();
                exit;
            }
        }

        public function getConnection() {
            return $this->conn;
        }

        public function getDbConfig() {
            return [
                'host' => $this->host,
                'dbname' => $this->dbname,
                'user' => $this->username,
                'pass' => $this->password
            ];
        }
    }
?>
