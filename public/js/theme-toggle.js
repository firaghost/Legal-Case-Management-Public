document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    const THEME_KEY = 'lcms-theme-preference';

    // Function to apply theme
    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (toggle) {
            toggle.checked = theme === 'dark';
        }
        localStorage.setItem(THEME_KEY, theme);
        
        // Add transition class for smooth theme changes
        document.body.classList.add('theme-transition');
        
        // Force refresh dropdown styling
        const selects = document.querySelectorAll('select');
        selects.forEach(select => {
            // Trigger a repaint to ensure styling is applied
            select.style.display = 'none';
            select.offsetHeight; // Trigger reflow
            select.style.display = '';
        });
        
        setTimeout(() => {
            document.body.classList.remove('theme-transition');
        }, 300);
    }

    // Initialize theme from localStorage or default to light
    let storedTheme = localStorage.getItem(THEME_KEY) || 'light';
    applyTheme(storedTheme);

    // Add toggle event listener if toggle exists
    if (toggle) {
        toggle.addEventListener('change', function() {
            const newTheme = toggle.checked ? 'dark' : 'light';
            applyTheme(newTheme);
        });
    }

    // Listen for theme changes from other tabs/windows
    window.addEventListener('storage', function(e) {
        if (e.key === THEME_KEY && e.newValue) {
            applyTheme(e.newValue);
        }
    });
}); 