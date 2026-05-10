document.addEventListener('DOMContentLoaded', () => {
    // Get all form inputs and error spans
    const form    = document.getElementById('signupForm');
    const nameIn  = form.querySelector('[name="full_name"]');
    const emailIn = form.querySelector('[name="email"]');
    const phoneIn = form.querySelector('[name="phone"]');
    const passIn  = form.querySelector('[name="password"]');
    const confIn  = form.querySelector('[name="confirm_password"]');

    const err = (id, msg) => {
        const el = document.getElementById(id);
        el.textContent = msg;
        el.style.cssText = 'color:red;font-size:0.82em;';
    };
    const ok = (id) => { document.getElementById(id).textContent = ''; };

    nameIn.addEventListener('input',  () => nameIn.value.trim().length >= 2 ? ok('nameErr')  : err('nameErr',  'At least 2 characters.'));
    emailIn.addEventListener('input', () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailIn.value.trim()) ? ok('emailErr') : err('emailErr', 'Invalid email.'));
    phoneIn.addEventListener('input', () => {
        if (!phoneIn.value) return ok('phoneErr');
        /^05\d{8}$/.test(phoneIn.value) ? ok('phoneErr') : err('phoneErr', 'Format: 05xxxxxxxx');
    });
    passIn.addEventListener('input',  () => passIn.value.length >= 6 ? ok('passErr')    : err('passErr',    'Min 6 characters.'));
    confIn.addEventListener('input',  () => confIn.value === passIn.value ? ok('confirmErr') : err('confirmErr', 'Passwords do not match.'));

    // Final check on submit before sending to server
    form.addEventListener('submit', (e) => {
        let valid = true;
        if (nameIn.value.trim().length < 2)                              { err('nameErr',  'At least 2 characters.'); valid = false; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailIn.value.trim()))   { err('emailErr', 'Invalid email.');          valid = false; }
        if (phoneIn.value && !/^05\d{8}$/.test(phoneIn.value))          { err('phoneErr', 'Format: 05xxxxxxxx');      valid = false; }
        if (passIn.value.length < 6)                                     { err('passErr',  'Min 6 characters.');       valid = false; }
        if (confIn.value !== passIn.value)                               { err('confirmErr','Passwords do not match.'); valid = false; }
        if (!valid) e.preventDefault();
    });
});
