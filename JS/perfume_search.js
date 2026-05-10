document.addEventListener('DOMContentLoaded', () => {
    // Get search input and form
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('searchForm');
    if (!input || !form) return;

    let timer;
    // Submit form after user stops typing for 500ms
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 500);
    });
});
