const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Number that counts up to its target: x-data="counter(1500)" x-intersect.once="start()".
 */
export function counter(target) {
    return {
        display: '0',

        start() {
            if (prefersReducedMotion()) {
                this.display = target.toLocaleString('es-PE');
                return;
            }

            const duration = 1600;
            const startedAt = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - startedAt) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                this.display = Math.round(target * eased).toLocaleString('es-PE');

                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        },
    };
}

/**
 * x-reveal: the element fades in and slides up the first time it enters the screen.
 */
export function reveal(el) {
    if (prefersReducedMotion()) {
        return;
    }

    const hiddenClasses = ['opacity-0', 'translate-y-6'];
    el.classList.add('transition', 'duration-700', 'ease-out', ...hiddenClasses);

    const observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            el.classList.remove(...hiddenClasses);
            observer.disconnect();
        }
    }, { threshold: 0.15 });

    observer.observe(el);
}
