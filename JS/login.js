document.addEventListener('DOMContentLoaded', () => {
    // Get form inputs and error elements
    const form     = document.getElementById('loginForm');
    const emailIn  = form.querySelector('[name="email"]');
    const passIn   = form.querySelector('[name="password"]');
    const emailErr = document.getElementById('emailErr');
    const passErr  = document.getElementById('passErr');

    function showError(el, msg) {
        el.textContent = msg;
        el.style.color = 'red';
        el.style.fontSize = '0.85em';
    }
    function clearError(el) { el.textContent = ''; }

    emailIn.addEventListener('input', () => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        re.test(emailIn.value.trim()) ? clearError(emailErr) : showError(emailErr, 'Enter a valid email.');
    });

    passIn.addEventListener('input', () => {
        passIn.value.length >= 6 ? clearError(passErr) : showError(passErr, 'At least 6 characters.');
    });

    // Validate on submit before sending to server
    form.addEventListener('submit', (e) => {
        let valid = true;
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!re.test(emailIn.value.trim())) {
            showError(emailErr, 'Enter a valid email.');
            valid = false;
        }
        if (passIn.value.length < 6) {
            showError(passErr, 'At least 6 characters.');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
});
