const monkey = document.getElementById('monkey');
const gameContainer = document.querySelector('.game-container');
const scoreElement = document.getElementById('score');
const chancesElement = document.getElementById('chances');
const timerElement = document.getElementById('timer');
const mathPopup = document.getElementById('mathPopup');
const gameOverPopup = document.getElementById('gameOverPopup');
const successPopup = document.getElementById('successPopup');
const equationImage = document.getElementById('equationImage');
const answerInput = document.getElementById('answer');
const submitAnswer = document.getElementById('submitAnswer');
const finalScoreElement = document.getElementById('finalScore');
const restartButton = document.getElementById('restartButton');
const pauseButton = document.createElement('button');

// Add pause button to game container
pauseButton.textContent = '⏸️';
pauseButton.className = 'pause-button';
gameContainer.appendChild(pauseButton);

let score = 0;
let gameSpeed = 3;
let isGameOver = false;
let isPaused = false;
let bananas = [];
let currentSolution = null;
let gameLoop = null;
let chances = 3;
let timerInterval = null;
let timeLeft = 30;

// Monkey movement
let monkeyLeft = 320;  // Set to match CSS initial position
const monkeySpeed = 20;

// Set initial position when the page loads
monkey.style.left = monkeyLeft + 'px';

document.addEventListener('keydown', (e) => {
    if (isGameOver || isPaused) return;
    
    if (e.key === 'ArrowLeft') {
        monkeyLeft = Math.max(0, monkeyLeft - monkeySpeed);
    } else if (e.key === 'ArrowRight') {
        monkeyLeft = Math.min(640, monkeyLeft + monkeySpeed);
    }
    
    monkey.style.left = monkeyLeft + 'px';
});

// Create banana element
function createBanana() {
    const banana = document.createElement('div');
    banana.className = 'banana';
    banana.style.left = Math.random() * 640 + 'px';
    banana.style.top = '-30px';
    gameContainer.appendChild(banana);
    return banana;
}

// Get a random equation from the API
async function getMathEquation() {
    try {
        const response = await fetch('api/get_equation.php');
        if (!response.ok) {
            throw new Error('Failed to fetch equation');
        }
        const data = await response.json();
        return {
            image: data.question,
            solution: data.solution
        };
    } catch (error) {
        console.error('Error fetching equation:', error);
        throw error;
    }
}

// Save score to database
async function saveScore(finalScore) {
    try {
        const response = await fetch('api/save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                score: finalScore,
                level: Math.floor(score / 5) + 1 // Calculate level based on score
            })
        });

        if (!response.ok) {
            throw new Error('Failed to save score');
        }

        const data = await response.json();
        console.log('Score saved:', data);
    } catch (error) {
        console.error('Error saving score:', error);
    }
}

// Start timer for math equation
function startTimer() {
    timeLeft = 30;
    timerElement.textContent = timeLeft;
    timerElement.parentElement.classList.remove('warning');
    
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 10) {
            timerElement.parentElement.classList.add('warning');
        }
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            showGameOver();
        }
    }, 1000);
}

// Show math popup and pause game
async function showMathPopup() {
    isPaused = true;
    try {
        const mathData = await getMathEquation();
        currentSolution = mathData.solution;
        equationImage.src = mathData.image;
        answerInput.value = '';
        
        // Ensure clean state before showing popup
        clearInterval(timerInterval);
        mathPopup.style.display = 'flex';
        answerInput.focus();
        startTimer();
    } catch (error) {
        console.error('Error showing math popup:', error);
        // If there's an error, resume the game
        isPaused = false;
    }
}

function updateHeartsDisplay() {
    const hearts = '❤️'.repeat(chances);
    chancesElement.textContent = hearts;
}

// Show success popup
function showSuccessPopup() {
    // Reduce chances when answer is correct
    chances--;
    updateHeartsDisplay();
    
    successPopup.style.display = 'flex';
    setTimeout(() => {
        successPopup.style.display = 'none';
        // Resume game state after success message
        isPaused = false;
        
        // Wait a short moment before spawning new banana
        setTimeout(() => {
            // Create new banana and add to array
            const newBanana = createBanana();
            bananas.push(newBanana);
            // Restart spawn timer
            setTimeout(spawnBanana, Math.random() * 2000 + 1000);
        }, 100);
    }, 1500);
}

// Handle answer submission
submitAnswer.addEventListener('click', () => {
    const userAnswer = parseInt(answerInput.value);
    
    // Clear timer immediately
    clearInterval(timerInterval);
    
    if (userAnswer === currentSolution) {
        // Hide math popup first
        mathPopup.style.display = 'none';
        answerInput.value = '';
        // Show success message and reduce chances
        showSuccessPopup();
    } else {
        showGameOver();
    }
});

// Handle Enter key in answer input
answerInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        submitAnswer.click();
    }
});

// Show game over popup
async function showGameOver() {
    isGameOver = true;
    isPaused = true;
    finalScoreElement.textContent = score;
    
    // Clear any existing timers
    clearInterval(timerInterval);
    
    // Hide other popups
    mathPopup.style.display = 'none';
    successPopup.style.display = 'none';
    
    // Show game over popup
    gameOverPopup.style.display = 'flex';
    
    // Stop all game loops
    if (gameLoop) {
        cancelAnimationFrame(gameLoop);
    }
    
    // Remove all bananas
    bananas.forEach(banana => banana.remove());
    bananas = [];

    // Save final score to database
    await saveScore(score);
}

// Reset game state
function resetGame() {
    // Reset variables
    score = 0;
    gameSpeed = 3;
    isGameOver = false;
    isPaused = false;
    bananas = [];
    currentSolution = null;
    monkeyLeft = 320;
    chances = 3;
    clearInterval(timerInterval);
    
    // Reset UI
    scoreElement.textContent = '0';
    updateHeartsDisplay();
    monkey.style.left = monkeyLeft + 'px';
    gameOverPopup.style.display = 'none';
    mathPopup.style.display = 'none';
    successPopup.style.display = 'none';
    answerInput.value = '';
    pauseButton.textContent = '⏸️';
    
    // Restart game loops
    moveBananas();
    spawnBanana();
}

// Initialize hearts at game start
updateHeartsDisplay();

// Restart game
restartButton.addEventListener('click', resetGame);

// Banana movement and collision detection
function moveBananas() {
    if (isGameOver || isPaused) {
        // Keep the animation loop running even when paused
        gameLoop = requestAnimationFrame(moveBananas);
        return;
    }
    
    bananas.forEach((banana, index) => {
        const bananaTop = parseInt(window.getComputedStyle(banana).getPropertyValue('top'));
        
        // Move banana down
        banana.style.top = (bananaTop + gameSpeed) + 'px';
        
        // Check for collision with monkey
        if (bananaTop > 520) {
            const monkeyRect = monkey.getBoundingClientRect();
            const bananaRect = banana.getBoundingClientRect();
            
            if (
                bananaRect.left < monkeyRect.right &&
                bananaRect.right > monkeyRect.left &&
                bananaRect.top < monkeyRect.bottom &&
                bananaRect.bottom > monkeyRect.top
            ) {
                // Catch successful
                score++;
                scoreElement.textContent = score;
                banana.remove();
                bananas.splice(index, 1);
                
                // Increase game speed every 5 points
                if (score % 5 === 0) {
                    gameSpeed += 0.5;
                }
            } else if (bananaTop > 600) {
                // Banana missed
                banana.remove();
                bananas.splice(index, 1);
                
                // If chances is exactly 0 and we miss a banana, game over immediately
                if (chances === 0) {
                    showGameOver();
                } else {
                    showMathPopup();
                }
            }
        }
    });
    
    gameLoop = requestAnimationFrame(moveBananas);
}

// Spawn new bananas periodically
function spawnBanana() {
    if (isGameOver || isPaused) return;
    
    const banana = createBanana();
    bananas.push(banana);
    
    // Spawn next banana after random delay between 1-3 seconds
    setTimeout(spawnBanana, Math.random() * 2000 + 1000);
}

// Start the game
moveBananas();
spawnBanana();

// Pause button click handler
pauseButton.addEventListener('click', () => {
    if (isGameOver) return;
    
    isPaused = !isPaused;
    pauseButton.textContent = isPaused ? '▶️' : '⏸️';
    
    if (!isPaused) {
        // Resume game
        spawnBanana();
    }
}); 