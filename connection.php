<?php
require_once __DIR__ . '/vendor/autoload.php';

class Database {
    private static $instance = null;
    private $client;
    private $db;

    private function __construct() {
        try {
            // Connect to MongoDB
            $this->client = new MongoDB\Client("mongodb://localhost:27017/");
            $this->db = $this->client->quizquest;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            die("Failed to connect to database: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCollection($collectionName) {
        return $this->db->$collectionName;
    }

    public function getUsers() {
        return $this->db->Users;
    }

    public function getQuestions() {
        return $this->db->Questions;
    }
}