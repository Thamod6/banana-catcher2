<?php
require_once 'includes/session.php';

// If user is already logged in, redirect to menu
if (isLoggedIn()) {
    header("Location: menu.php");
    exit();
}

// Check for remember me cookie
if (isset($_COOKIE['remember_token'])) {
    require_once 'config/database.php';
    require_once 'models/User.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $result = $user->loginWithToken($_COOKIE['remember_token']);

    if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        header("Location: menu.php");
        exit();
    }
}

// Get remembered username if exists
$remembered_username = isset($_COOKIE['remembered_username']) ? $_COOKIE['remembered_username'] : '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    require_once 'models/User.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $user->username = $_POST['username'] ?? '';
    $user->password = $_POST['password'] ?? '';

    $result = $user->login();

    if ($result) {
        // Login successful
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];

        // Handle remember me
        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
            $token = bin2hex(random_bytes(32)); // Generate a secure random token
            $user->saveRememberToken($result['id'], $token);
            
            // Set cookies with proper path and domain
            setcookie('remember_token', $token, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            setcookie('remembered_username', $result['username'], [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        } else {
            // If remember me is not checked, clear any existing remembered username
            setcookie('remembered_username', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        header("Location: menu.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Catcher - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="login.css?v=1">
</head>
<body>
    <div class="monkey-decoration"></div>
    
    <div class="game-title">
        <h1>BANANA CATCHER</h1>
    </div>
    
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($remembered_username); ?>">
                <i class="fas fa-user"></i>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember" <?php echo $remembered_username ? 'checked' : ''; ?>>
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html> 