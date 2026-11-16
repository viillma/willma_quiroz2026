import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function useReveal(targets, options = {}) {
    const {
        y = 44,
        scale = 1,
        stagger = 0,
        start = 'top 85%',
        duration = 1,
    } = options;

    if (
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    const list = Array.isArray(targets) ? targets : [targets];
    const first = list[0];

    if (!first) {
        return;
    }

    gsap.fromTo(
        list,
        { opacity: 0, y, scale },
        {
            opacity: 1,
            y: 0,
            scale: 1,
            duration,
            ease: 'power3.out',
            stagger,
            scrollTrigger: {
                trigger: first,
                start,
                toggleActions: 'play none none none',
            },
        },
    );
}

export function useRevealGroup(trigger, targets, options = {}) {
    const { y = 40, scale = 1, stagger = 0.1, start = 'top 82%' } = options;

    if (
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    if (!trigger || !targets?.length) {
        return;
    }

    gsap.fromTo(
        targets,
        { opacity: 0, y, scale },
        {
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.9,
            ease: 'power3.out',
            stagger,
            scrollTrigger: {
                trigger,
                start,
                toggleActions: 'play none none none',
            },
        },
    );
}
