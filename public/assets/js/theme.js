document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;
    
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme) {
      htmlElement.setAttribute('data-theme', savedTheme);
      updateThemeIcon(savedTheme === 'dark');
    } else {
      const initialTheme = prefersDark ? 'dark' : 'light';
      htmlElement.setAttribute('data-theme', initialTheme);
      updateThemeIcon(prefersDark);
    }
    
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = htmlElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      htmlElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeIcon(newTheme === 'dark');
    });
    
    function updateThemeIcon(isDark) {
      if (isDark) {
        themeIcon.classList.remove('ph-sun');
        themeIcon.classList.add('ph-moon');
      } else {
        themeIcon.classList.remove('ph-moon');
        themeIcon.classList.add('ph-sun');
      }
    }
  });
