const portfolioRoot = document.querySelector('.portfolio-site');

if (portfolioRoot instanceof HTMLElement) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const desktopMedia = window.matchMedia('(min-width: 64rem)');

    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    /* -------------------------------------------------------------------------
     * 1. Subtle Hero Scroll Parallax (cached geometry, zero scroll reflows)
     * ---------------------------------------------------------------------- */
    const parallaxScene = portfolioRoot.querySelector('[data-parallax-scene]');
    const parallaxLayers = parallaxScene
        ? Array.from(parallaxScene.querySelectorAll('[data-parallax-speed]')).map((el) => ({
            el,
            speed: Number(el.getAttribute('data-parallax-speed') || 0),
        }))
        : [];
    let sceneTop = 0;
    let sceneHeight = 0;

    const resetParallax = () => {
        parallaxLayers.forEach(({ el }) => {
            el.style.removeProperty('transform');
        });
    };

    const measureParallax = () => {
        if (!parallaxScene) {
            return;
        }
        const rect = parallaxScene.getBoundingClientRect();
        sceneTop = window.scrollY + rect.top;
        sceneHeight = rect.height;
    };

    const updateParallax = (scrollY) => {
        if (!parallaxScene || !parallaxLayers.length || reducedMotion.matches) {
            return;
        }

        const delta = scrollY - sceneTop;
        if (delta < -120 || delta > sceneHeight + 120) {
            return;
        }

        const clamped = clamp(delta, -120, sceneHeight);
        for (let i = 0; i < parallaxLayers.length; i += 1) {
            const { el, speed } = parallaxLayers[i];
            const y = clamp(clamped * speed, -44, 56);
            el.style.transform = `translate3d(0, ${y.toFixed(1)}px, 0)`;
        }
    };

    /* -------------------------------------------------------------------------
     * 2. Pinned Experience Chapter HUD (cached offsets)
     * ---------------------------------------------------------------------- */
    const experienceSection = portfolioRoot.querySelector('[data-pinned-experience]');
    const experienceItems = experienceSection
        ? Array.from(experienceSection.querySelectorAll('[data-experience-item]')).map((el) => ({
            el,
            org: el.getAttribute('data-organization') || '',
            top: 0,
        }))
        : [];
    const experienceLabel = experienceSection?.querySelector('[data-experience-label]');
    const experienceCounter = experienceSection?.querySelector('[data-experience-counter]');
    const experienceProgress = experienceSection?.querySelector('[data-experience-progress]');
    let lastExperienceIndex = -1;

    const measureExperience = () => {
        experienceItems.forEach((entry) => {
            entry.top = window.scrollY + entry.el.getBoundingClientRect().top;
        });
    };

    const updateExperienceHud = (scrollY) => {
        if (experienceItems.length < 2 || !experienceLabel || !experienceCounter || !experienceProgress) {
            return;
        }

        const readingY = scrollY + window.innerHeight * 0.36;
        let activeIndex = 0;

        for (let i = 0; i < experienceItems.length; i += 1) {
            if (experienceItems[i].top <= readingY) {
                activeIndex = i;
            }
        }

        if (activeIndex === lastExperienceIndex) {
            return;
        }

        lastExperienceIndex = activeIndex;
        const total = experienceItems.length;
        experienceLabel.textContent = experienceItems[activeIndex].org;
        experienceCounter.textContent = `${String(activeIndex + 1).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
        experienceProgress.style.transform = `scaleX(${((activeIndex + 1) / total).toFixed(4)})`;
    };

    /* -------------------------------------------------------------------------
     * 3. Pinned Horizontal Scroll Showcase (#bukti — zero reflow per frame)
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

    const HEADER_OFFSET = 68;
    let pinnedMode = false;
    let trackStart = 0;
    let scrollableDistance = 1;
    let maxTranslate = 0;
    let currentIndex = -1;
    let lastOffset = -1;

    const updateHorizontalUi = (progress, index) => {
        const clampedIndex = clamp(index, 0, Math.max(0, cards.length - 1));

        if (progressBar && cards.length > 0) {
            const minFill = 1 / cards.length;
            const fill = minFill + (1 - minFill) * clamp(progress, 0, 1);
            progressBar.style.transform = `scaleX(${fill.toFixed(4)})`;
        }

        if (clampedIndex === currentIndex) {
            return;
        }

        currentIndex = clampedIndex;

        if (counter && cards.length > 0) {
            counter.textContent = `${String(currentIndex + 1).padStart(2, '0')} / ${String(cards.length).padStart(2, '0')}`;
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
        showcase.classList.add('is-horizontal-enhanced');
        showcase.classList.toggle('is-horizontal-pinned', shouldPin);

        if (!shouldPin) {
            pinnedMode = false;
            maxTranslate = 0;
            lastOffset = -1;
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
        lastOffset = -1;

        const pinHeight = Math.max(pin.offsetHeight, window.innerHeight - HEADER_OFFSET);
        scrollableDistance = Math.max(1, maxTranslate);
        track.style.height = `${Math.round(pinHeight + scrollableDistance)}px`;
        trackStart = window.scrollY + track.getBoundingClientRect().top - HEADER_OFFSET;

        updateHorizontalScroll(window.scrollY);
    };

    const updateHorizontalScroll = (scrollY) => {
        if (!pinnedMode || !rail || maxTranslate <= 0) {
            return;
        }

        const progress = clamp((scrollY - trackStart) / scrollableDistance, 0, 1);
        const offset = Math.round(progress * maxTranslate * 2) / 2;

        if (offset !== lastOffset) {
            lastOffset = offset;
            rail.style.transform = `translate3d(${-offset}px, 0, 0)`;
        }

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

        if (pinnedMode && maxTranslate > 0) {
            const fraction = cards.length > 1 ? clampedIndex / (cards.length - 1) : 0;
            window.scrollTo({
                top: Math.round(trackStart + fraction * scrollableDistance),
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
            });
            return;
        }

        if (viewport) {
            const cardLeft = targetCard.offsetLeft - rail.offsetLeft;
            viewport.scrollTo({
                left: Math.max(0, cardLeft),
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
            });
            updateHorizontalUi(cards.length > 1 ? clampedIndex / (cards.length - 1) : 1, clampedIndex);
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
     * Unified rAF Scroll Driver (zero DOM reads inside scroll callback)
     * ---------------------------------------------------------------------- */
    let rafId = 0;
    const onScroll = () => {
        if (rafId) {
            return;
        }

        rafId = requestAnimationFrame(() => {
            rafId = 0;
            const scrollY = window.scrollY;
            updateParallax(scrollY);
            updateExperienceHud(scrollY);
            updateHorizontalScroll(scrollY);
        });
    };

    const measureAll = () => {
        if (reducedMotion.matches) {
            resetParallax();
        }
        measureParallax();
        measureExperience();
        syncHorizontalLayout();
        const scrollY = window.scrollY;
        updateParallax(scrollY);
        updateExperienceHud(scrollY);
    };

    let resizeTimer = 0;
    const onResize = () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(measureAll, 120);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('load', measureAll, { once: true });
    reducedMotion.addEventListener('change', measureAll);
    desktopMedia.addEventListener('change', measureAll);

    if (document.fonts?.ready) {
        document.fonts.ready.then(measureAll);
    }

    measureAll();
}
