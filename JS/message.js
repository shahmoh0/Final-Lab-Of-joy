document.addEventListener('DOMContentLoaded', () => {
    // Get message form elements and preview elements
    const msgText     = document.getElementById('msgText');
    const fontType    = document.getElementById('fontType');
    const fontColor   = document.getElementById('fontColor');
    const charCount   = document.getElementById('charCount');
    const previewText = document.getElementById('previewText');
    const previewFont = document.getElementById('previewFont');
    const previewStyle= document.getElementById('previewStyle');
    const msgErr      = document.getElementById('msgErr');

    // Get all card style radio buttons
    const styleRadios = document.querySelectorAll('[name="card_style"]');

    function updatePreview() {
        if (previewText) {
            previewText.textContent  = msgText.value || '...';
            previewText.style.color  = fontColor.value;
            previewText.style.fontFamily = fontType.value;
        }
        if (previewFont) previewFont.textContent = 'Font: ' + fontType.value;
    }

    function updateCharCount() {
        const len = msgText.value.length;
        charCount.textContent = len + ' / 500';
        charCount.style.color = len > 450 ? 'orange' : '';
    }

    // Update preview on text, font, or color change
    if (msgText) {
        msgText.addEventListener('input', () => {
            updatePreview();
            updateCharCount();
            msgText.value.trim() ? (msgErr.textContent = '') : (msgErr.textContent = 'Message cannot be empty.');
        });
        updateCharCount();
    }

    if (fontType)  fontType.addEventListener('change',  updatePreview);
    if (fontColor) fontColor.addEventListener('input',  updatePreview);

    styleRadios.forEach(r => r.addEventListener('change', () => {
        if (previewStyle) previewStyle.textContent = r.value;
    }));

    // Block submit if message is empty
    const form = document.getElementById('messageForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!msgText.value.trim()) {
                msgErr.textContent = 'Message cannot be empty.';
                msgErr.style.cssText = 'color:red;font-size:0.82em;';
                e.preventDefault();
            }
        });
    }
});
