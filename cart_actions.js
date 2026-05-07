document.addEventListener('DOMContentLoaded', () => {
    // Fade out the success message after 2.5 seconds
    const msg = document.querySelector('p[style*="color:green"]');
    if (msg) {
        setTimeout(() => {
            msg.style.transition = 'opacity 1s';
            msg.style.opacity = '0';
        }, 2500);
    }
});
