// App Layout Scripts
document.addEventListener('DOMContentLoaded', function () {
   const navbar = document.querySelector('.floating-navbar');
   const subNavbar = document.querySelector('.sub-navbar');

   function adjustSubNavbar() {
      if (navbar && subNavbar) {
         const navHeight = navbar.offsetHeight;
         const navTop = navbar.offsetTop;
         // Posicionar justo debajo del navbar, restando unos pixeles para que "cuelgue" visualmente
         // y se vea la conexión (overlap)
         subNavbar.style.top = (navTop + navHeight - 2) + 'px';
      }
   }

   // Ajustar al cargar y al redimensionar
   adjustSubNavbar();
   window.addEventListener('resize', adjustSubNavbar);

   // Observer para cambios en el tamaño del navbar (ej. si carga algo dinámico)
   const resizeObserver = new ResizeObserver(() => {
      adjustSubNavbar();
   });

   if (navbar) {
      resizeObserver.observe(navbar);
   }
});
