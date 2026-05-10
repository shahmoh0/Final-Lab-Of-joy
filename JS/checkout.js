document.addEventListener('DOMContentLoaded', () => {

    // Confirm before removing item from checkout page
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('Remove this item from your order?')) {
                e.preventDefault();
            }
        });
    });

    // Confirm before emptying cart from checkout page
    const emptyForm = document.getElementById('emptyCartForm');
    if (emptyForm) {
        emptyForm.addEventListener('submit', (e) => {
            if (!confirm('Empty your entire cart?')) {
                e.preventDefault();
            }
        });
    }

    // Auto-submit qty form on change
    document.querySelectorAll('.qty-form').forEach(form => {
        const input = form.querySelector('.qty-input');
        if (input) {
            input.addEventListener('change', () => {
                if (parseInt(input.value) >= 1) form.submit();
            });
        }
    });

    // Validate delivery form before buy
    const form    = document.getElementById('checkoutForm');
    if (!form) return;

    const nameIn  = form.querySelector('[name="full_name"]');
    const emailIn = form.querySelector('[name="email"]');
    const phoneIn = form.querySelector('[name="phone"]');
    const postalIn= form.querySelector('[name="postal_code"]');

    const err = (id, msg) => {
        const el = document.getElementById(id);
        if (el) { el.textContent = msg; el.style.cssText = 'color:red;font-size:0.82em;'; }
    };
    const ok = (id) => { const el = document.getElementById(id); if (el) el.textContent = ''; };

    nameIn.addEventListener('input',  () => nameIn.value.trim().length >= 2 ? ok('nameErr')  : err('nameErr',  'Name is required.'));
    emailIn.addEventListener('input', () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailIn.value.trim()) ? ok('emailErr') : err('emailErr', 'Invalid email.'));
    phoneIn.addEventListener('input', () => /^05\d{8}$/.test(phoneIn.value) ? ok('phoneErr') : err('phoneErr', 'Format: 05xxxxxxxx'));
    postalIn.addEventListener('input',() => {
        if (!postalIn.value) return ok('postalErr');
        /^\d{5}$/.test(postalIn.value) ? ok('postalErr') : err('postalErr', 'Must be 5 digits.');
    });

    form.addEventListener('submit', (e) => {
        let valid = true;
        if (nameIn.value.trim().length < 2)                            { err('nameErr',  'Name is required.');   valid = false; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailIn.value.trim())) { err('emailErr', 'Invalid email.');       valid = false; }
        if (!/^05\d{8}$/.test(phoneIn.value))                         { err('phoneErr', 'Format: 05xxxxxxxx');   valid = false; }
        if (postalIn.value && !/^\d{5}$/.test(postalIn.value))        { err('postalErr','Must be 5 digits.');    valid = false; }
        if (!valid) e.preventDefault();
    });

});
