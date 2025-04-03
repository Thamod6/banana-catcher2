<?php
require_once 'includes/session.php';

// If user is already logged in, redirect to menu
if (isLoggedIn()) {
    header("Location: menu.php");
    exit();
}

$error_message = '';
$success_message = '';
$token_valid = false;

// Check if token exists in URL
if (isset($_GET['token'])) {
    require_once 'config/database.php';
    require_once 'models/User.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $result = $user->verifyResetToken($_GET['token']);

    if ($result) {
        $token_valid = true;
    } else {
        $error_message = "Invalid or expired reset link. Please request a new password reset.";
    }
} else {
    $error_message = "No reset token provided. Please use the link from your email.";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $error_message = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        if ($user->resetPassword($_GET['token'], $new_password)) {
            $success_message = "Password has been reset successfully. You can now login with your new password.";
            $token_valid = false; // Prevent form from showing after successful reset
        } else {
            $error_message = "An error occurred while resetting your password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Catcher - Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="forgot-password.css">
</head>
<body>
    <div class="monkey-decoration"></div>
    
    <div class="game-title">
        <h1>BANANA CATCHER</h1>
    </div>
    
    <div class="forgot-password-container">
        <h2>Reset Password</h2>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($token_valid): ?>
            <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-lock"></i>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-lock"></i>
                </div>
                
                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
        <?php endif; ?>
        
        <div class="back-to-login">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
</body>
</html> 