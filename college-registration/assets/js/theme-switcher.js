document.addEventListener('DOMContentLoaded', function() {
    // Get theme switcher elements
    const themeOptions = document.querySelectorAll('.theme-option');
    const themeStyle = document.getElementById('theme-style');
    
    // Set initial theme from localStorage or default
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    
    // Add click event to all theme options
    themeOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            setTheme(theme);
            localStorage.setItem('theme', theme);
        });
    });
    
    // Function to set theme
    function setTheme(theme) {
        // Update stylesheet href
        themeStyle.href = `assets/css/themes/${theme}.css`;
        
        // Update active state in dropdown
        themeOptions.forEach(opt => {
            opt.classList.toggle('active', opt.getAttribute('data-theme') === theme);
        });
        
        // Add theme class to body
        document.body.className = theme + '-theme';
    }
});