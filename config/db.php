<?php
/**
 * Database Connection Class
 * Handles MySQL database connection using PDO
 */

class Database
{
    private $host = "";
    private $db_name = "";
    private $username = "";      // Default XAMPP username
    private $password = "";          // Default XAMPP password (empty)
    public $conn;

    // Get database connection
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password 
            );
            return $this->conn;
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
            return null;
        }
    }
}

// Create connection instance
$db = new Database();
$pdo = $db->getConnection();

if ($pdo === null) {
    die("<strong>Database Connection Failed!</strong><br>Make sure MySQL is running and database 'train_booking' exists.");
}
?>
