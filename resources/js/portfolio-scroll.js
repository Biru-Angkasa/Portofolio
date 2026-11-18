const scrollSections = document.querySelectorAll('.portfolio-site [data-network-background] .section-title');

if (scrollSections.length) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const active = new Set();
    const headings = [];
    const clamp = (value) => Math.max(0, Math.min(1, value));
    const header = document.querySelector('.portfolio-site > header');
    const navigation = Array.from(document.querySelectorAll('#site-nav a[href]')).flatMap((link) => {
        const section = document.getElementById(new URL(link.href).hash.slice(1));

        return section ? [{ link, section }] : [];
    });
    const timeline = document.querySelector('#pengalaman ol');
    const timelineItems = timeline ? Array.from(timeline.children).filter((item) => item.querySelector('h3')) : [];
    let currentSection = null;

    if (timelineItems.length > 1) {
        timeline.classList.add('portfolio-timeline');
    }

    const progress = document.createElement('div');
    progress.className = 'portfolio-scroll-progress';
    progress.setAttribute('aria-hidden', 'true');
    header?.append(progress);

    scrollSections.forEach((heading) => {
        const accessible = document.createElement('span');
        accessible.className = 'sr-only';
        accessible.textContent = heading.textContent;
        const visual = document.createElement('span');
        visual.setAttribute('aria-hidden', 'true');
        heading.textContent.split(/(\s+)/u).forEach((word) => {
            if (/^\s+$/u.test(word)) {
                visual.append(document.createTextNode(word));
                return;
            }
            const element = document.createElement('span');
            element.className = 'scroll-reveal-word';
            element.textContent = word;
            visual.append(element);
        });
        heading.replaceChildren(accessible, visual);
        heading.setAttribute('data-scroll-reveal', '');
        headings.push(heading);
    });

    const updateHeading = (heading) => {
        const words = heading.querySelectorAll('.scroll-reveal-word');
        const top = heading.getBoundingClientRect().top;
        const reveal = reducedMotion.matches ? 1 : clamp((window.innerHeight * 0.9 - top) / (window.innerHeight * 0.32));
        words.forEach((word, index) => {
            const amount = clamp(reveal * (words.length + 0.7) - index + 0.3);
            word.style.setProperty('--word-opacity', (0.3 + amount * 0.7).toFixed(3));
            word.style.setProperty('--word-blur', `${((1 - amount) * 2).toFixed(2)}px`);
            word.style.setProperty('--word-lift', `${((1 - amount) * 8).toFixed(2)}px`);
        });
    };

    let frame = 0;
    const update = () => {
        frame = 0;
        const distance = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.scale = `${distance > 0 ? clamp(window.scrollY / distance) : 0} 1`;
        active.forEach(updateHeading);

        const readingLine = (header?.getBoundingClientRect().height ?? 80) + window.innerHeight * 0.25;
        let selected = navigation[0];
        navigation.forEach((entry) => {
            if (entry.section.getBoundingClientRect().top <= readingLine) {
                selected = entry;
            }
        });

        if (distance > 0 && window.scrollY >= distance - 4) {
            selected = navigation.at(-1);
        }

        if (selected && selected.section !== currentSection) {
            currentSection = selected.section;
            navigation.forEach(({ link, section }) => {
                if (section === currentSection) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        }

        if (timelineItems.length > 1) {
            const bounds = timeline.getBoundingClientRect();
            if (bounds.bottom >= 0 && bounds.top <= window.innerHeight) {
                const start = timelineItems[0].offsetTop + 8;
                const length = timelineItems.at(-1).offsetTop + 8 - start;
                const amount = reducedMotion.matches ? 1 : clamp((readingLine - bounds.top - start) / Math.max(1, length));
                timeline.style.setProperty('--timeline-length', `${length}px`);
                timeline.style.setProperty('--timeline-progress', amount.toFixed(4));
                timelineItems.forEach((item) => {
                    item.classList.toggle('is-timeline-read', reducedMotion.matches || item.offsetTop + 8 <= readingLine - bounds.top);
                });
            }
        }
    };
    const schedule = () => {
        if (!frame) {
            frame = requestAnimationFrame(update);
        }
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(({ target, isIntersecting }) => {
            if (isIntersecting) {
                active.add(target);
            } else {
                active.delete(target);
            }
            updateHeading(target);
        });
        schedule();
    }, { rootMargin: '100px 0px' });
    headings.forEach((heading) => observer.observe(heading));
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    const layoutObserver = new ResizeObserver(schedule);
    layoutObserver.observe(document.querySelector('.portfolio-site'));
    reducedMotion.addEventListener('change', () => {
        headings.forEach(updateHeading);
        schedule();
    });
    document.fonts.ready.then(schedule);
    schedule();
}
