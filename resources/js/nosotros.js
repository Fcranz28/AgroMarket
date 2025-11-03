document.addEventListener('DOMContentLoaded', function() {
    // Inicializar AOS (Animate On Scroll)
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Animación de contadores para las estadísticas
    const stats = document.querySelectorAll('.stat-number');
    
    function animateStats() {
        stats.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            const duration = 2000; // Duración de la animación en ms
            const step = target / (duration / 16); // 60 FPS
            let current = 0;

            const updateCount = () => {
                if (current < target) {
                    current += step;
                    if (current > target) current = target;
                    stat.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCount);
                }
            };

            updateCount();
        });
    }

    // Observador de Intersección para iniciar la animación cuando las estadísticas sean visibles
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateStats();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    // Observar la sección de estadísticas
    const impactSection = document.querySelector('.impact-stats');
    if (impactSection) {
        observer.observe(impactSection);
    }

    // Efecto parallax para las secciones hero y CTA
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-section');
        const ctaSection = document.querySelector('.cta-section');

        if (heroSection) {
            heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
        }
        if (ctaSection) {
            ctaSection.style.backgroundPositionY = (scrolled - ctaSection.offsetTop) * 0.5 + 'px';
        }
    });

    // Animación hover para las tarjetas de valores
    const valueCards = document.querySelectorAll('.value-card');
    valueCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.querySelector('.value-icon').style.transform = 'scale(1.2) rotate(10deg)';
        });
        card.addEventListener('mouseleave', () => {
            card.querySelector('.value-icon').style.transform = 'scale(1) rotate(0deg)';
        });
    });
});
