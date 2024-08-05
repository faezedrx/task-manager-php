<?php
if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $mysqli;

        private $host = 'localhost';
        private $db = 'bamboos1_services_portf';
        private $user = 'bamboos1_faezeh';
        private $pass = 'Fa+Ba!M1402';

        private function __construct() {
            $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);
            if ($this->mysqli->connect_error) {
                die("Connection failed: " . $this->mysqli->connect_error);
            }
            $this->mysqli->set_charset("utf8mb4");
        }

        public static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new Database();
            }
            return self::$instance;
        }

        public function getConnection() {
            return $this->mysqli;
        }
    }
}
?>
