<?php
    // Singleton class for PDO connection
    class dbconnection {
        private static $instance = null;
        private $pdo;

        private function __construct($host, $dbname, $username, $password) {
            try {
                $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        public static function getInstance($host = null, $dbname = null, $username = null, $password = null) {
            if (self::$instance === null) {
                if ($host === null || $dbname === null || $username === null || $password === null) {
                    throw new Exception("Database connection parameters are required for the first call to getInstance.");
                }
                self::$instance = new dbconnection($host, $dbname, $username, $password);
            }
            return self::$instance;
        }

        public function getPdo() {
            return $this->pdo;
        }

        public function getDbName() {
            return $this->pdo->query('SELECT DATABASE()')->fetchColumn();
        }
    }
?>