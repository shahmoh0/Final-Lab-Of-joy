document.addEventListener('DOMContentLoaded', () => {

    // Confirm before any delete action
    document.querySelectorAll('.confirm-delete').forEach(link => {
        link.addEventListener('click', (e) => {
            const name = link.dataset.name || 'this item';
            if (!confirm('Delete ' + name + '? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Client-side validation for category form
    const catForm = document.getElementById('catForm');
    if (catForm) {
        catForm.addEventListener('submit', (e) => {
            const nameIn = catForm.querySelector('[name="name"]');
            const nameErr = document.getElementById('nameErr');
            if (nameIn.value.trim().length < 2) {
                nameErr.textContent = 'Name must be at least 2 characters.';
                e.preventDefault();
            } else {
                nameErr.textContent = '';
            }
        });
    }

    // Client-side validation for product form
    const prodForm = document.getElementById('prodForm');
    if (prodForm) {
        prodForm.addEventListener('submit', (e) => {
            let valid = true;
            const nameIn  = prodForm.querySelector('[name="name"]');
            const priceIn = prodForm.querySelector('[name="price"]');
            const nameErr  = document.getElementById('nameErr');
            const priceErr = document.getElementById('priceErr');

            if (nameIn.value.trim().length < 2) {
                nameErr.textContent = 'Name must be at least 2 characters.';
                valid = false;
            } else {
                nameErr.textContent = '';
            }

            if (parseFloat(priceIn.value) <= 0 || isNaN(parseFloat(priceIn.value))) {
                priceErr.textContent = 'Price must be greater than 0.';
                valid = false;
            } else {
                priceErr.textContent = '';
            }

            if (!valid) e.preventDefault();
        });
    }

    // Auto-hide alert messages after 4 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.8s';
            alert.style.opacity = '0';
        }, 4000);
    });

    // Client-side validation for user form
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', (e) => {
            let valid = true;
            const nameIn  = userForm.querySelector('[name="full_name"]');
            const emailIn = userForm.querySelector('[name="email"]');
            const passIn  = userForm.querySelector('[name="password"], [name="new_password"]');
            const nameErr  = document.getElementById('nameErr');
            const emailErr = document.getElementById('emailErr');
            const passErr  = document.getElementById('passErr');

            if (nameIn.value.trim().length < 2) {
                nameErr.textContent = 'Name must be at least 2 characters.';
                valid = false;
            } else { nameErr.textContent = ''; }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailIn.value.trim())) {
                emailErr.textContent = 'Invalid email address.';
                valid = false;
            } else { emailErr.textContent = ''; }

            if (passIn && passIn.required && passIn.value.length < 6) {
                passErr.textContent = 'Password must be at least 6 characters.';
                valid = false;
            } else if (passIn && passIn.value.length > 0 && passIn.value.length < 6) {
                passErr.textContent = 'Password must be at least 6 characters.';
                valid = false;
            } else if (passErr) { passErr.textContent = ''; }

            if (!valid) e.preventDefault();
        });
    }

});
