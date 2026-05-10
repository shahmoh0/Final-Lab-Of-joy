// Accessibility toolbar — Lab of Joy
// Persists user preferences in localStorage across all pages

(function () {

    const STORAGE_KEY = 'loj_a11y';
    const FONT_MIN    = 12;
    const FONT_MAX    = 26;
    const FONT_STEP   = 2;
    const FONT_DEFAULT= 16;

    // Load saved preferences
    function loadPrefs() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; }
        catch { return {}; }
    }

    // Save preferences
    function savePrefs(prefs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    }

    // Apply font size to root element
    function applyFontSize(size) {
        document.documentElement.style.fontSize = size + 'px';
    }

    // Build and inject the toolbar
    function buildBar() {
        const prefs = loadPrefs();
        let fontSize = prefs.fontSize || FONT_DEFAULT;

        // Apply saved preferences immediately
        applyFontSize(fontSize);
        if (prefs.highContrast)  document.body.classList.add('hc-mode');
        if (prefs.dyslexia)      document.body.classList.add('dyslexia-mode');

        // Create toolbar element
        const bar = document.createElement('div');
        bar.id = 'a11yBar';
        bar.setAttribute('role', 'toolbar');
        bar.setAttribute('aria-label', 'Accessibility options');

        bar.innerHTML = `
            <span class="a11yLabel">♿ Accessibility</span>

            <div class="a11yDivider"></div>

            <button id="a11yDecrease" aria-label="Decrease font size" title="Decrease font size">
                A−
            </button>

            <button id="a11yFontSize" aria-label="Current font size" aria-live="polite"
                    style="min-width:52px;text-align:center;" disabled>
                ${fontSize}px
            </button>

            <button id="a11yIncrease" aria-label="Increase font size" title="Increase font size">
                A+
            </button>

            <button id="a11yReset" aria-label="Reset font size" title="Reset to default">
                Reset
            </button>

            <div class="a11yDivider"></div>

            <button id="a11yContrast" aria-label="Toggle high contrast"
                    title="Toggle high contrast" class="${prefs.highContrast ? 'a11y-on' : ''}">
                ◑ Contrast
            </button>

            <button id="a11yDyslexia" aria-label="Toggle dyslexia-friendly font"
                    title="Dyslexia-friendly font" class="${prefs.dyslexia ? 'a11y-on' : ''}">
                Aa Dyslexia
            </button>
        `;

        // Insert as very first element in body
        document.body.insertBefore(bar, document.body.firstChild);

        const sizeDisplay = document.getElementById('a11yFontSize');

        // Decrease font size
        document.getElementById('a11yDecrease').addEventListener('click', () => {
            const prefs = loadPrefs();
            fontSize = Math.max(FONT_MIN, (prefs.fontSize || FONT_DEFAULT) - FONT_STEP);
            prefs.fontSize = fontSize;
            savePrefs(prefs);
            applyFontSize(fontSize);
            sizeDisplay.textContent = fontSize + 'px';
        });

        // Increase font size
        document.getElementById('a11yIncrease').addEventListener('click', () => {
            const prefs = loadPrefs();
            fontSize = Math.min(FONT_MAX, (prefs.fontSize || FONT_DEFAULT) + FONT_STEP);
            prefs.fontSize = fontSize;
            savePrefs(prefs);
            applyFontSize(fontSize);
            sizeDisplay.textContent = fontSize + 'px';
        });

        // Reset font size
        document.getElementById('a11yReset').addEventListener('click', () => {
            const prefs = loadPrefs();
            prefs.fontSize = FONT_DEFAULT;
            savePrefs(prefs);
            applyFontSize(FONT_DEFAULT);
            sizeDisplay.textContent = FONT_DEFAULT + 'px';
        });

        // Toggle high contrast
        document.getElementById('a11yContrast').addEventListener('click', function () {
            const prefs = loadPrefs();
            prefs.highContrast = !prefs.highContrast;
            savePrefs(prefs);
            document.body.classList.toggle('hc-mode', prefs.highContrast);
            this.classList.toggle('a11y-on', prefs.highContrast);
        });

        // Toggle dyslexia font
        document.getElementById('a11yDyslexia').addEventListener('click', function () {
            const prefs = loadPrefs();
            prefs.dyslexia = !prefs.dyslexia;
            savePrefs(prefs);
            document.body.classList.toggle('dyslexia-mode', prefs.dyslexia);
            this.classList.toggle('a11y-on', prefs.dyslexia);
        });
    }

    // Run after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildBar);
    } else {
        buildBar();
    }

})();
