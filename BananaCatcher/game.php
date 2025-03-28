<?php
require_once 'includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Catcher Game</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            background-attachment: fixed;
            filter: blur(8px) brightness(0.7);
            z-index: -1;
        }

        .game-container {
            width: 800px;
            height: 800px;
            position: relative;
            overflow: hidden;
            border: 4px solid #2E7D32;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
        }

        .score-container {
            position: fixed;
            top: 40px;
            left: 40px;
            color: white;
            font-size: 36px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px 40px;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
            z-index: 2;
        }

        .chances-container {
            position: fixed;
            top: 40px;
            right: 40px;
            color: white;
            font-size: 36px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px 40px;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
            z-index: 2;
        }

        .timer-container {
            font-size: 24px;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 15px;
            animation: pulse 1s infinite;
        }

        .timer-container.warning {
            color: #e74c3c;
            animation: pulse 0.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        #monkey {
            width: 160px;
            height: 160px;
            position: absolute;
            bottom: 40px;
            left: 320px;
            transition: left 0.1s linear;
            background-image: url('images/monkey.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            image-rendering: pixelated;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.4));
        }

        .banana {
            width: 80px;
            height: 80px;
            position: absolute;
            top: -80px;
            background-image: url('images/banana.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            transition: top 0.016s linear;
            image-rendering: pixelated;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            transform: rotate(0deg);
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .popup-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            max-width: 90%;
            width: 400px;
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .popup-content h2 {
            color: #2E7D32;
            margin-bottom: 20px;
            font-size: 32px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        #equationImage {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .popup-content input {
            padding: 10px;
            font-size: 18px;
            width: 150px;
            margin-bottom: 15px;
            border: 2px solid #4CAF50;
            border-radius: 5px;
        }

        .popup-content button {
            padding: 12px 30px;
            font-size: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .popup-content button:hover {
            background-color: #2E7D32;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup-content button:active {
            transform: translateY(0);
        }

        #gameOverPopup .popup-content {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        }

        #gameOverPopup h2 {
            color: #e74c3c;
            font-size: 36px;
        }

        #gameOverPopup p {
            font-size: 24px;
            margin: 20px 0;
            color: #2c3e50;
        }

        #gameOverPopup #finalScore {
            font-weight: bold;
            color: #e74c3c;
            font-size: 32px;
        }

        #gameOverPopup button {
            background-color: #e74c3c;
            padding: 15px 40px;
        }

        #gameOverPopup button:hover {
            background-color: #c0392b;
        }

        #successPopup .popup-content {
            background: linear-gradient(135deg, #ffffff 0%, #e8f5e9 100%);
            animation: popIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        #successPopup h2 {
            color: #2E7D32;
            font-size: 32px;
            margin-bottom: 15px;
        }

        #successPopup p {
            color: #388E3C;
            font-size: 24px;
            margin-bottom: 0;
        }

        @keyframes popIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pause-button {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #4CAF50;
            border: 3px solid #2E7D32;
            font-size: 32px;
            cursor: pointer;
            z-index: 10;
            padding: 5px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .pause-button:hover {
            background-color: #2E7D32;
            transform: translateX(-50%) scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .pause-button:active {
            transform: translateX(-50%) scale(0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .music-button {
            position: fixed;
            bottom: 20px;
            right: 40px;
            background: #4CAF50;
            border: 3px solid #2E7D32;
            font-size: 24px;
            cursor: pointer;
            z-index: 10;
            padding: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            color: white;
        }

        .music-button:hover {
            background-color: #2E7D32;
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .music-button:active {
            transform: scale(0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .music-button.muted i::before {
            content: "\f6a9";  /* Font Awesome muted icon */
        }

        .menu-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .menu-button:hover {
            background-color: #2E7D32;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="monkey-decoration"></div>
        
        <div class="score-container">
            Score: <span id="score">0</span>
        </div>
        
        <div class="chances-container">
            <span id="chances">❤️❤️❤️</span>
        </div>
        
        <button class="music-button muted">
            <i class="fas fa-volume-mute"></i>
        </button>
        
        <button class="pause-button">⏸️</button>
        
        <div id="monkey"></div>
        
        <audio id="gameMusic" preload="auto" loop>
            <source src="sounds/jungle-music.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>

    <div id="mathPopup" class="popup">
        <div class="popup-content">
            <h2>Solve the Math Problem!</h2>
            <div class="timer-container">
                Time: <span id="timer">30</span>
            </div>
            <img id="equationImage" src="http://marcconrad.com/uob/banana/api.php?out=json" alt="Math Equation">
            <input type="number" id="answer" placeholder="Enter your answer">
            <button id="submitAnswer">Submit</button>
        </div>
    </div>

    <div id="gameOverPopup" class="popup">
        <div class="popup-content">
            <h2>Game Over!</h2>
            <p>Final Score: <span id="finalScore">0</span></p>
            <button id="restartButton">Play Again</button>
            <a href="menu.php" class="menu-button">Back to Menu</a>
        </div>
    </div>

    <div id="successPopup" class="popup">
        <div class="popup-content">
            <h2>Correct!</h2>
            <p>Keep going!</p>
        </div>
    </div>

    <script src="music.js"></script>
    <script src="game.js"></script>
    <script>
        // Pass PHP user ID to JavaScript
        const userId = <?php echo $user_id; ?>;
    </script>
</body>
</html> 