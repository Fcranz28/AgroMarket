// Dashboard Layout Scripts
document.addEventListener('DOMContentLoaded', function () {
   // ===== THEME TOGGLE =====
   const themeToggle = document.getElementById('dashboardThemeToggle');
   const rootElement = document.documentElement;

   // Check for saved theme preference or default to system preference
   const savedTheme = localStorage.getItem('theme');
   const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

   // Set initial theme
   if (savedTheme) {
      rootElement.setAttribute('data-theme', savedTheme);
   } else if (systemPrefersDark) {
      rootElement.setAttribute('data-theme', 'dark');
   } else {
      rootElement.setAttribute('data-theme', 'light');
   }

   // Toggle theme on button click
   if (themeToggle) {
      console.log('Theme toggle button found');
      themeToggle.addEventListener('click', function (e) {
         e.preventDefault(); // Prevent any default behavior
         console.log('Theme toggle clicked');
         const currentTheme = rootElement.getAttribute('data-theme');
         console.log('Current theme:', currentTheme);
         const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
         console.log('New theme:', newTheme);

         rootElement.setAttribute('data-theme', newTheme);
         localStorage.setItem('theme', newTheme);

         // Add rotation animation
         const sunIcon = this.querySelector('.sun-icon');
         const moonIcon = this.querySelector('.moon-icon');

         this.style.transform = 'rotate(360deg)';
         setTimeout(() => {
            this.style.transform = '';
         }, 300);
      });
   } else {
      console.error('Theme toggle button NOT found');
   }

   // ===== MOBILE MENU TOGGLE =====
   const mobileMenuToggle = document.getElementById('mobileMenuToggle');
   const sidebar = document.querySelector('.sidebar');

   if (mobileMenuToggle && sidebar) {
      mobileMenuToggle.addEventListener('click', function () {
         sidebar.classList.toggle('active');
      });

      // Close sidebar when clicking outside
      document.addEventListener('click', function (event) {
         const isClickInsideSidebar = sidebar.contains(event.target);
         const isClickOnMenuButton = mobileMenuToggle.contains(event.target);

         if (!isClickInsideSidebar && !isClickOnMenuButton && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
         }
      });
   }
});
