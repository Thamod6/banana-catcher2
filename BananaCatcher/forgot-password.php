<?php
require_once 'includes/session.php';
require_once 'send-reset-email.php';

// If user is already logged in, redirect to menu
if (isLoggedIn()) {
    header("Location: menu.php");
    exit();
}

$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    require_once 'models/User.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = "Please enter your email address.";
    } else {
        // Check if email exists in database
        if ($user->emailExists($email)) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+5 hour'));
            
            // Save reset token to database
            if ($user->saveResetToken($email, $reset_token, $reset_expires)) {
                // Send reset email
                if (sendResetEmail($email, $reset_token)) {
                    $success_message = "If an account exists with this email, you will receive password reset instructions.";
                } else {
                    $error_message = "An error occurred while sending the email. Please try again later.";
                }
            } else {
                $error_message = "An error occurred. Please try again later.";
            }
        } else {
            // Don't reveal if email exists or not for security
            $success_message = "If an account exists with this email, you will receive password reset instructions.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Catcher - Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="forgot-password.css">
</head>
<body>
    <div class="monkey-decoration"></div>
    
    <div class="game-title">
        <h1>BANANA CATCHER</h1>
    </div>
    
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="forgot-password.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
                <i class="fas fa-envelope"></i>
            </div>
            
            <button type="submit" class="submit-btn">Send Reset Link</button>
        </form>
        
        <div class="back-to-login">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
</body>
</html> 