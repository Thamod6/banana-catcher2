<?php
require_once 'config/database.php';
require_once 'models/User.php';

try {
    // Test database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo "Database connection successful!<br>";
        
        // Test user creation
        $user = new User($db);
        $user->username = "testuser" . time(); // Unique username
        $user->email = "test" . time() . "@example.com"; // Unique email
        $user->password = "Test123!";
        
        if($user->create()) {
            echo "User creation successful!<br>";
            
            // Verify user exists
            if($user->usernameExists()) {
                echo "Username exists check working!<br>";
            }
            
            if($user->emailExists()) {
                echo "Email exists check working!<br>";
            }
            
            // Test login
            $login_result = $user->login();
            if($login_result) {
                echo "Login test successful!<br>";
                echo "User ID: " . $login_result['id'] . "<br>";
                echo "Username: " . $login_result['username'] . "<br>";
            }
        } else {
            echo "User creation failed!<br>";
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 