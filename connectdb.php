<?php
    class Connection {
        private $host = 'localhost';
        private $db = 'project_maverick';
        private $user = 'root';
        private $pass = '';
        private $charset = 'utf8mb4';
        private $pdo;
        private $port = 3308;
        private $error;

        public function __construct() {       
            $dsn = "mysql:host=$this->host;dbname=$this->db;port=3308;charset=$this->charset";     
            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass);
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        }

        public function getConnection() {
            return $this->pdo;
        }

        public function closeConnection() {
            $this->pdo = null;
        }
    }
?>