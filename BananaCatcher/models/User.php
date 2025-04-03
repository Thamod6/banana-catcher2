<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $created_at;
    public $last_login;
    public $remember_token;
    public $reset_token;
    public $reset_expires;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password";

        $stmt = $this->conn->prepare($query);

        // Sanitize and hash
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login user
    public function login() {
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                // Update last login
                $this->updateLastLogin($row['id']);
                return $row;
            }
        }
        return false;
    }

    // Update last login timestamp
    private function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table_name . "
                SET last_login = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
    }

    // Check if username exists
    public function usernameExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Save remember token
    public function saveRememberToken($user_id, $token) {
        $query = "UPDATE " . $this->table_name . "
                SET remember_token = :token,
                    last_login = CURRENT_TIMESTAMP
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Login with remember token
    public function loginWithToken($token) {
        $query = "SELECT id, username FROM " . $this->table_name . " WHERE remember_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Update last login
            $this->updateLastLogin($row['id']);
            return $row;
        }
        return false;
    }

    // Clear remember token
    public function clearRememberToken($user_id) {
        $query = "UPDATE " . $this->table_name . "
                SET remember_token = NULL
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Save reset token
    public function saveResetToken($email, $token, $expires) {
        $query = "UPDATE " . $this->table_name . "
                SET reset_token = :token,
                    reset_expires = :expires
                WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expires", $expires);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }

    // Verify reset token
    public function verifyResetToken($token) {
        $query = "SELECT id, email FROM " . $this->table_name . "
                WHERE reset_token = :token
                AND reset_expires > CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Reset password
    public function resetPassword($token, $new_password) {
        $user = $this->verifyResetToken($token);
        if(!$user) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . "
                SET password = :password,
                    reset_token = NULL,
                    reset_expires = NULL
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $user['id']);
        return $stmt->execute();
    }
}
?> 