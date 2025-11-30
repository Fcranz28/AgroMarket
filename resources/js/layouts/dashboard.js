// Dashboard Layout Scripts
document.addEventListener('DOMContentLoaded', function () {
   // ===== THEME TOGGLE =====
   const themeToggle = document.getElementById('themeToggle');
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
      themeToggle.addEventListener('click', function () {
         const currentTheme = rootElement.getAttribute('data-theme');
         const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

         rootElement.setAttribute('data-theme', newTheme);
         localStorage.setItem('theme', newTheme);

         // Add rotation animation
         this.style.transform = 'rotate(360deg)';
         setTimeout(() => {
            this.style.transform = '';
         }, 300);
      });
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
