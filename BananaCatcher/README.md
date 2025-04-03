# Banana Catcher Game

A fun educational game where you catch bananas while solving math problems! Built with HTML5, CSS, JavaScript, and PHP.

## Prerequisites

- XAMPP (or similar local server with PHP support)
- Web browser (Chrome, Firefox, Safari, etc.)

## Installation

1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Clone or download this repository
3. Place the game files in your XAMPP's `htdocs` folder:
   - If using XAMPP, copy the files to: `C:\xampp\htdocs\BananaCatcher\`
   - Make sure all files maintain their folder structure

## Running the Game

1. Start XAMPP Control Panel
2. Start Apache server by clicking the "Start" button next to Apache
3. Open your web browser
4. Navigate to: `http://localhost/BananaCatcher/`

## How to Play

- Use LEFT and RIGHT arrow keys to move the monkey
- Catch falling bananas
- When you catch a banana, solve the math problem that appears
- You have 3 chances and 30 seconds to solve each problem
- Score increases as you catch more bananas
- Game speed increases every 5 points
- Game ends when you run out of chances or time

## Features

- Real-time math problems from external API
- Score tracking
- Multiple lives system
- Increasing difficulty
- Pause functionality
- Responsive design

## File Structure

```
BananaCatcher/
├── api/
│   ├── get_equation.php
│   └── save_score.php
├── includes/
│   └── session.php
├── images/
│   └── (game images)
├── game.php
├── game.js
├── styles.css
└── README.md
```

## Troubleshooting

If you encounter any issues:

1. Make sure XAMPP's Apache server is running
2. Check if all files are in the correct locations
3. Verify that you have an active internet connection (required for math problems API)
4. Clear your browser cache if the game doesn't load properly

## Credits

- Math problems provided by [Marc Conrad's Banana API](http://marcconrad.com/uob/banana/api.php)
- Game assets and design by [Your Name/Team] 