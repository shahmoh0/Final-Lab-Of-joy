document.addEventListener('DOMContentLoaded', () => {
    // Ask for confirmation before removing a cart item
    document.querySelectorAll('form[action="cart.php"]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('Remove this item from your cart?')) {
                e.preventDefault();
            }
        });
    });
});
