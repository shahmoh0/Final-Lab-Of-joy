document.addEventListener('DOMContentLoaded', () => {
    // Fade out the added-to-cart message after 2.5 seconds
    const msg = document.querySelector('.added-msg');
    if (msg) {
        setTimeout(() => {
            msg.style.transition = 'opacity 1s';
            msg.style.opacity = '0';
        }, 2500);
    }
});
