// connection.php
<?php
require_once __DIR__ . '/vendor/autoload.php';

class Database {
    private static $instance = null;
    private $client;
    private $db;

    private function __construct() {
        try {
            $this->client = new MongoDB\Client("mongodb://localhost:27017");
            $this->db = $this->client->Quiz_Quest;
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getDB() {
        return $this->db;
    }
}
?>
