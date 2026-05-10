document.addEventListener('DOMContentLoaded', () => {
    // Get budget input and summary tags
    const budgetIn  = document.getElementById('budgetInput');
    const budgetErr = document.getElementById('budgetErr');
    const totalTag  = document.getElementById('totalTag');
    const remainTag = document.getElementById('remainTag');

    function updateRemaining() {
        if (!totalTag || !remainTag) return;
        const budget = parseFloat(budgetIn.value) || 0;
        const total  = parseFloat(totalTag.textContent.replace(/[^0-9.]/g, '')) || 0;
        const rem    = Math.max(0, budget - total);
        remainTag.textContent = 'Remaining: SAR ' + rem.toFixed(2);
        remainTag.style.color = rem < 0 ? 'red' : '';
    }

    // Recalculate remaining budget on input change
    if (budgetIn) {
        budgetIn.addEventListener('input', () => {
            const v = parseFloat(budgetIn.value);
            if (isNaN(v) || v < 50) {
                budgetErr.textContent = 'Budget must be at least 50 SAR.';
                budgetErr.style.cssText = 'color:red;font-size:0.82em;';
            } else {
                budgetErr.textContent = '';
            }
            updateRemaining();
        });
    }

    // Block submit if budget is out of range
    const form = document.getElementById('customForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            const v = parseFloat(budgetIn.value);
            if (isNaN(v) || v < 50) {
                budgetErr.textContent = 'Budget must be at least 50 SAR.';
                budgetErr.style.cssText = 'color:red;font-size:0.82em;';
                e.preventDefault();
            }
        });
    }
});
