const portfolioRoot = document.querySelector('.portfolio-site');

if (portfolioRoot instanceof HTMLElement) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const desktopMedia = window.matchMedia('(min-width: 64rem)');

    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    /* -------------------------------------------------------------------------
     * 1. Subtle Hero Scroll Parallax
     * ---------------------------------------------------------------------- */
    const parallaxScene = portfolioRoot.querySelector('[data-parallax-scene]');
    const parallaxLayers = parallaxScene
        ? Array.from(parallaxScene.querySelectorAll('[data-parallax-speed]'))
        : [];

    const resetParallax = () => {
        parallaxLayers.forEach((layer) => {
            layer.style.removeProperty('transform');
        });
    };

    const updateParallax = () => {
        if (!parallaxScene || !parallaxLayers.length || reducedMotion.matches) {
            return;
        }

        const rect = parallaxScene.getBoundingClientRect();
        if (rect.bottom < -120 || rect.top > window.innerHeight + 120) {
            return;
        }

        const scrollOffset = clamp(-rect.top, -120, rect.height);
        parallaxLayers.forEach((layer) => {
            const speed = Number(layer.getAttribute('data-parallax-speed') || 0);
            const y = clamp(scrollOffset * speed, -44, 56);
            layer.style.transform = `translate3d(0, ${y.toFixed(1)}px, 0)`;
        });
    };

    /* -------------------------------------------------------------------------
     * 2. Pinned Experience Chapter HUD
     * ---------------------------------------------------------------------- */
    const experienceSection = portfolioRoot.querySelector('[data-pinned-experience]');
    const experienceItems = experienceSection
        ? Array.from(experienceSection.querySelectorAll('[data-experience-item]'))
        : [];
    const experienceLabel = experienceSection?.querySelector('[data-experience-label]');
    const experienceCounter = experienceSection?.querySelector('[data-experience-counter]');
    const experienceProgress = experienceSection?.querySelector('[data-experience-progress]');

    const updateExperienceHud = () => {
        if (experienceItems.length < 2 || !experienceLabel || !experienceCounter || !experienceProgress) {
            return;
        }

        const readingY = window.innerHeight * 0.36;
        let activeIndex = 0;

        experienceItems.forEach((item, index) => {
            if (item.getBoundingClientRect().top <= readingY) {
                activeIndex = index;
            }
        });

        const total = experienceItems.length;
        const org = experienceItems[activeIndex]?.getAttribute('data-organization') || '';
        experienceLabel.textContent = org;
        experienceCounter.textContent = `${String(activeIndex + 1).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
        experienceProgress.style.transform = `scaleX(${((activeIndex + 1) / total).toFixed(4)})`;
    };

    /* -------------------------------------------------------------------------
     * 3. Pinned Horizontal Scroll Showcase (#bukti)
     * ---------------------------------------------------------------------- */
    const showcase = portfolioRoot.querySelector('[data-horizontal-showcase]');
    const track = showcase?.querySelector('[data-horizontal-track]');
    const pin = showcase?.querySelector('[data-horizontal-pin]');
    const viewport = showcase?.querySelector('[data-horizontal-viewport]');
    const rail = showcase?.querySelector('[data-horizontal-rail]');
    const prevButton = showcase?.querySelector('[data-horizontal-prev]');
    const nextButton = showcase?.querySelector('[data-horizontal-next]');
    const counter = showcase?.querySelector('[data-horizontal-counter]');
    const progressBar = showcase?.querySelector('[data-horizontal-bar]');
    const cards = rail ? Array.from(rail.children) : [];

    let pinnedMode = false;
    let maxTranslate = 0;
    let currentIndex = 0;

    const updateHorizontalUi = (progress, index) => {
        currentIndex = clamp(index, 0, Math.max(0, cards.length - 1));

        if (counter && cards.length > 0) {
            counter.textContent = `${String(currentIndex + 1).padStart(2, '0')} / ${String(cards.length).padStart(2, '0')}`;
        }

        if (progressBar && cards.length > 0) {
            const minFill = 1 / cards.length;
            const fill = minFill + (1 - minFill) * clamp(progress, 0, 1);
            progressBar.style.transform = `scaleX(${fill.toFixed(4)})`;
        }

        if (prevButton instanceof HTMLButtonElement) {
            prevButton.disabled = currentIndex <= 0;
        }

        if (nextButton instanceof HTMLButtonElement) {
            nextButton.disabled = currentIndex >= cards.length - 1;
        }
    };

    const syncHorizontalLayout = () => {
        if (!showcase || !track || !pin || !viewport || !rail || cards.length < 2) {
            return;
        }

        const shouldPin = desktopMedia.matches && !reducedMotion.matches;
        showcase.classList.toggle('is-horizontal-enhanced', !reducedMotion.matches);
        showcase.classList.toggle('is-horizontal-pinned', shouldPin);

        if (!shouldPin) {
            pinnedMode = false;
            maxTranslate = 0;
            track.style.removeProperty('height');
            rail.style.removeProperty('transform');
            updateMobileCarouselState();
            return;
        }

        rail.style.removeProperty('transform');
        const overflow = Math.max(0, rail.scrollWidth - viewport.clientWidth);

        if (overflow < 40) {
            pinnedMode = false;
            maxTranslate = 0;
            showcase.classList.remove('is-horizontal-pinned');
            track.style.removeProperty('height');
            updateHorizontalUi(1, cards.length - 1);
            return;
        }

        pinnedMode = true;
        maxTranslate = overflow;
        const pinHeight = Math.max(pin.offsetHeight, window.innerHeight - 72);
        track.style.height = `${Math.round(pinHeight + maxTranslate)}px`;
        updateHorizontalScroll();
    };

    const updateHorizontalScroll = () => {
        if (!pinnedMode || !track || !rail || maxTranslate <= 0) {
            return;
        }

        const rect = track.getBoundingClientRect();
        const headerOffset = 72;
        const scrollable = Math.max(1, track.offsetHeight - Math.max(pin.offsetHeight, window.innerHeight - headerOffset));
        const progress = clamp((headerOffset - rect.top) / scrollable, 0, 1);
        const offset = progress * maxTranslate;

        rail.style.transform = `translate3d(${(-offset).toFixed(1)}px, 0, 0)`;

        const estimatedIndex = Math.round(progress * (cards.length - 1));
        updateHorizontalUi(progress, estimatedIndex);
    };

    const updateMobileCarouselState = () => {
        if (!viewport || pinnedMode || cards.length < 2) {
            return;
        }

        const maxScroll = Math.max(1, viewport.scrollWidth - viewport.clientWidth);
        const progress = clamp(viewport.scrollLeft / maxScroll, 0, 1);
        const index = Math.round(progress * (cards.length - 1));
        updateHorizontalUi(progress, index);
    };

    const scrollToCard = (targetIndex) => {
        const clampedIndex = clamp(targetIndex, 0, cards.length - 1);
        const targetCard = cards[clampedIndex];
        if (!targetCard) {
            return;
        }

        if (pinnedMode && track && pin && maxTranslate > 0) {
            const headerOffset = 72;
            const scrollable = Math.max(1, track.offsetHeight - Math.max(pin.offsetHeight, window.innerHeight - headerOffset));
            const fraction = cards.length > 1 ? clampedIndex / (cards.length - 1) : 0;
            const trackTop = window.scrollY + track.getBoundingClientRect().top;
            window.scrollTo({
                top: Math.round(trackTop - headerOffset + fraction * scrollable),
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
            });
            return;
        }

        if (viewport) {
            viewport.scrollTo({
                left: targetCard.offsetLeft,
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
            });
        }
    };

    prevButton?.addEventListener('click', () => scrollToCard(currentIndex - 1));
    nextButton?.addEventListener('click', () => scrollToCard(currentIndex + 1));
    viewport?.addEventListener('scroll', updateMobileCarouselState, { passive: true });

    cards.forEach((card, index) => {
        card.addEventListener('focusin', () => {
            if (pinnedMode && index !== currentIndex) {
                scrollToCard(index);
            }
        });
    });

    /* -------------------------------------------------------------------------
     * Unified rAF Scroll & Resize Driver
     * ---------------------------------------------------------------------- */
    let rafId = 0;
    const onScroll = () => {
        if (rafId) {
            return;
        }

        rafId = requestAnimationFrame(() => {
            rafId = 0;
            updateParallax();
            updateExperienceHud();
            updateHorizontalScroll();
        });
    };

    const onEnvironmentChange = () => {
        if (reducedMotion.matches) {
            resetParallax();
        } else {
            updateParallax();
        }
        syncHorizontalLayout();
        updateExperienceHud();
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', syncHorizontalLayout, { passive: true });
    reducedMotion.addEventListener('change', onEnvironmentChange);
    desktopMedia.addEventListener('change', onEnvironmentChange);

    if (pin && 'ResizeObserver' in window) {
        new ResizeObserver(() => {
            if (pinnedMode) {
                syncHorizontalLayout();
            }
        }).observe(pin);
    }

    onEnvironmentChange();
}
