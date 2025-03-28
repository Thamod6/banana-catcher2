<?php
require_once 'includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get username from session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Game - Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <style>
        .menu-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            z-index: 1000;
        }

        .welcome-text {
            font-size: 2em;
            color: #333;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .menu-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .menu-button {
            background: #ffd700;
            color: #333;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 1.2em;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: bold;
        }

        .menu-button:hover {
            background: #ffed4a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }

        .menu-button i {
            font-size: 1.3em;
        }

        .logout-btn {
            background: #ff4444;
            color: white;
        }

        .logout-btn:hover {
            background: #cc0000;
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.4);
        }
    </style>
</head>
<body>
    <div class="monkey-decoration"></div>
    
    <div class="menu-container">
        <h2 class="welcome-text">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        
        <div class="menu-buttons">
            <a href="game.php" class="menu-button">
                <i class="fas fa-gamepad"></i>
                Play Game
            </a>
            
            <a href="scoreboard.php" class="menu-button">
                <i class="fas fa-trophy"></i>
                Scoreboard
            </a>
            
            <a href="logout.php" class="menu-button logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
</body>
</html> 