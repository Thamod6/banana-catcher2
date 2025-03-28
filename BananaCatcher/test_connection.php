<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo "Database connection successful!<br>";
        
        // Test users table
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Number of users in database: " . $row['count'] . "<br>";
        
        // Test scores table
        $query = "SELECT COUNT(*) as count FROM scores";
        $stmt = $db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Number of scores in database: " . $row['count'] . "<br>";
        
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 