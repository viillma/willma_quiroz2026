import Lenis from 'lenis';

let lenis = null;

export function initSmoothScroll() {
    if (lenis) {
        return lenis;
    }

    lenis = new Lenis({
        duration: 1.35,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        wheelMultiplier: 0.9,
        touchMultiplier: 1.4,
    });

    return lenis;
}

export function destroySmoothScroll() {
    if (lenis) {
        lenis.destroy();
        lenis = null;
    }
}

export function getLenis() {
    return lenis;
}

export function scrollToSection(hash) {
    const target = document.querySelector(hash);

    if (!target) {
        return;
    }

    if (lenis) {
        lenis.scrollTo(target, { offset: -84, duration: 1.4 });
    } else {
        target.scrollIntoView({ behavior: 'smooth' });
    }
}
