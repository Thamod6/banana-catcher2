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
    <title>Banana Catcher - Scoreboard</title>
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

        .scoreboard-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 800px;
            margin: 20px;
            backdrop-filter: blur(10px);
        }

        .scoreboard-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .scoreboard-header h1 {
            color: #2E7D32;
            font-size: 36px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .scoreboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .scoreboard-table th,
        .scoreboard-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 2px solid #4CAF50;
        }

        .scoreboard-table th {
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
        }

        .scoreboard-table tr:nth-child(even) {
            background-color: rgba(76, 175, 80, 0.1);
        }

        .scoreboard-table tr:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }

        .rank {
            font-weight: bold;
            color: #2E7D32;
        }

        .current-user {
            background-color: rgba(76, 175, 80, 0.2) !important;
            font-weight: bold;
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

        .refresh-button {
            background: none;
            border: none;
            color: #4CAF50;
            font-size: 24px;
            cursor: pointer;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .refresh-button:hover {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="scoreboard-container">
        <div class="scoreboard-header">
            <h1>Scoreboard</h1>
            <button class="refresh-button" onclick="refreshScoreboard()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        
        <table class="scoreboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody id="scoreboardBody">
                <!-- Scores will be loaded here via AJAX -->
            </tbody>
        </table>

        <div style="text-align: center;">
            <a href="menu.php" class="menu-button">Back to Menu</a>
        </div>
    </div>

    <script>
        function refreshScoreboard() {
            fetch('api/get_scores.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('scoreboardBody');
                    tbody.innerHTML = '';
                    
                    data.forEach((score, index) => {
                        const row = document.createElement('tr');
                        if (score.user_id === <?php echo $user_id; ?>) {
                            row.classList.add('current-user');
                        }
                        
                        row.innerHTML = `
                            <td class="rank">#${index + 1}</td>
                            <td>${score.username}</td>
                            <td>${score.score}</td>
                        `;
                        
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error loading scores:', error));
        }

        // Load scores when page loads
        refreshScoreboard();
    </script>
</body>
</html> 