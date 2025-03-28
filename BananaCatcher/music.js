document.addEventListener('DOMContentLoaded', () => {
    const musicButton = document.querySelector('.music-button');
    const musicIcon = musicButton.querySelector('i');
    const gameMusic = document.getElementById('gameMusic');
    let isMuted = true;

    // Set initial volume
    gameMusic.volume = 0.5;

    // Update icon class based on mute state
    function updateIcon() {
        if (isMuted) {
            musicIcon.className = 'fas fa-volume-mute';
        } else {
            musicIcon.className = 'fas fa-volume-up';
        }
    }

    // Set initial icon state
    updateIcon();

    musicButton.addEventListener('click', () => {
        if (isMuted) {
            // Try to play the audio
            const playPromise = gameMusic.play();
            
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    musicButton.classList.remove('muted');
                    isMuted = false;
                    updateIcon();
                }).catch(error => {
                    console.log('Audio playback failed:', error);
                });
            }
        } else {
            gameMusic.pause();
            musicButton.classList.add('muted');
            isMuted = true;
            updateIcon();
        }
    });

    // Handle audio loading error
    gameMusic.addEventListener('error', (e) => {
        console.error('Error loading audio:', e);
        musicButton.style.display = 'none'; // Hide button if audio fails to load
    });
}); 