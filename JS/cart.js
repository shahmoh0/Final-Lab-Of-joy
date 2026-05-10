document.addEventListener('DOMContentLoaded', () => {

    // Confirm before removing a single item
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('Remove this item from your cart?')) {
                e.preventDefault();
            }
        });
    });

    // Confirm before emptying the entire cart
    const emptyForm = document.getElementById('emptyCartForm');
    if (emptyForm) {
        emptyForm.addEventListener('submit', (e) => {
            if (!confirm('Empty your entire cart? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    }

    // Auto-submit qty form when input loses focus
    document.querySelectorAll('.qty-form').forEach(form => {
        const input = form.querySelector('.qty-input');
        if (input) {
            input.addEventListener('change', () => {
                if (parseInt(input.value) >= 1) form.submit();
            });
        }
    });

});
