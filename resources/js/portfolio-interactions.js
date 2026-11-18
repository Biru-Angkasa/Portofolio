const site = document.querySelector('.portfolio-site');

if (site instanceof HTMLElement) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const canPoint = () => finePointer.matches && !reducedMotion.matches;

    site.querySelectorAll('[data-tech-text]').forEach((target) => {
        const heading = target.closest('h1, h2, h3, h4, h5, h6');
        if (heading instanceof HTMLElement && !heading.hasAttribute('aria-label')) {
            heading.setAttribute('aria-label', heading.textContent.trim());
        }
        const text = target.textContent;
        const accessible = document.createElement('span');
        accessible.className = 'sr-only';
        accessible.textContent = text;
        const visual = document.createElement('span');
        visual.setAttribute('aria-hidden', 'true');
        visual.className = 'tech-wordmark';
        const letters = [];
        let drag = null;

        text.split(/(\s+)/u).forEach((word) => {
            if (/^\s+$/u.test(word)) {
                visual.append(document.createTextNode(word));
                return;
            }

            const wordElement = document.createElement('span');
            wordElement.className = 'tech-word';
            Array.from(word).forEach((character) => {
                const letter = document.createElement('span');
                letter.className = 'tech-letter';
                const fill = document.createElement('span');
                fill.className = 'tech-letter-fill';
                fill.textContent = character;
                const baseline = document.createElement('span');
                baseline.className = 'tech-baseline';
                fill.append(baseline);
                const outline = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                outline.classList.add('tech-letter-outline');
                outline.setAttribute('focusable', 'false');
                const glyph = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                glyph.textContent = character;
                outline.append(glyph);
                const tether = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                tether.classList.add('tech-drag-tether');
                const connector = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                tether.append(connector);
                const bounds = document.createElement('span');
                bounds.className = 'tech-drag-bounds';
                const offset = document.createElement('span');
                offset.className = 'tech-drag-offset';
                bounds.append(offset);
                for (let index = 0; index < 4; index += 1) {
                    const handle = document.createElement('span');
                    handle.className = 'tech-drag-handle';
                    bounds.append(handle);
                }
                letter.append(tether, outline, fill, bounds);
                wordElement.append(letter);
                letters.push({ letter, baseline, glyph, connector, bounds, offset });
            });
            visual.append(wordElement);
        });

        target.replaceChildren(accessible, visual);

        const measure = () => {
            if (drag) {
                return;
            }
            letters.forEach(({ letter, baseline, glyph, bounds }) => {
                glyph.setAttribute('y', String(baseline.getBoundingClientRect().top - letter.getBoundingClientRect().top));
                const box = glyph.getBBox();
                bounds.style.left = `${box.x}px`;
                bounds.style.top = `${box.y}px`;
                bounds.style.width = `${box.width}px`;
                bounds.style.height = `${box.height}px`;
            });
        };
        new ResizeObserver(measure).observe(target.parentElement);
        document.fonts.ready.then(measure);
        measure();

        let frame = 0;
        let pointerX = 0;
        const releaseTimers = new Map();
        const finishDrag = (event, immediate = false) => {
            if (!drag || (event && event.pointerId !== drag.pointerId)) {
                return;
            }
            const { letter, pointerId } = drag;
            drag = null;
            if (letter.hasPointerCapture(pointerId)) {
                letter.releasePointerCapture(pointerId);
            }
            letter.classList.remove('is-dragging');
            letter.classList.toggle('is-returning', !immediate);
            letter.style.setProperty('--tech-drag-x', '0px');
            letter.style.setProperty('--tech-drag-y', '0px');
            if (!immediate) {
                releaseTimers.set(letter, setTimeout(() => {
                    letter.classList.remove('is-returning');
                    releaseTimers.delete(letter);
                }, 650));
            }
        };

        letters.forEach(({ letter, connector, offset }) => {
            letter.addEventListener('pointerdown', (event) => {
                if (reducedMotion.matches || event.button !== 0 || drag) {
                    return;
                }
                if (event.pointerType === 'mouse') {
                    event.preventDefault();
                }
                clearTimeout(releaseTimers.get(letter));
                releaseTimers.delete(letter);
                const bounds = letter.getBoundingClientRect();
                const heroContainer = target.closest('#beranda') ?? target.parentElement;
                if (!(heroContainer instanceof HTMLElement)) {
                    return;
                }
                const hero = heroContainer.getBoundingClientRect();
                drag = { letter, pointerId: event.pointerId, startX: event.clientX, startY: event.clientY, bounds, hero };
                letter.classList.remove('is-returning');
                letter.classList.add('is-dragging');
                letter.style.setProperty('--tech-drag-x', '0px');
                letter.style.setProperty('--tech-drag-y', '0px');
                offset.textContent = '+0, +0';
                connector.setAttribute('x1', String(bounds.width / 2));
                connector.setAttribute('y1', String(bounds.height / 2));
                connector.setAttribute('x2', String(bounds.width / 2));
                connector.setAttribute('y2', String(bounds.height / 2));
                letter.setPointerCapture(event.pointerId);
            });
            letter.addEventListener('pointermove', (event) => {
                if (drag?.letter !== letter || event.pointerId !== drag.pointerId) {
                    return;
                }
                const { bounds, hero } = drag;
                const x = Math.max(hero.left + 12 - bounds.left, Math.min(hero.right - 12 - bounds.right, event.clientX - drag.startX));
                const y = Math.max(Math.max(hero.top + 12, 90) - bounds.top, Math.min(220, hero.bottom - 20 - bounds.bottom, event.clientY - drag.startY));
                letter.style.setProperty('--tech-drag-x', `${x}px`);
                letter.style.setProperty('--tech-drag-y', `${y}px`);
                offset.textContent = `${x >= 0 ? '+' : ''}${Math.round(x)}, ${y >= 0 ? '+' : ''}${Math.round(y)}`;
                connector.setAttribute('x2', String(bounds.width / 2 + x));
                connector.setAttribute('y2', String(bounds.height / 2 + y));
            }, { passive: true });
            letter.addEventListener('pointerup', finishDrag);
            letter.addEventListener('pointercancel', finishDrag);
            letter.addEventListener('lostpointercapture', finishDrag);
        });

        const reset = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            letters.forEach(({ letter }) => letter.style.removeProperty('--tech-proximity'));
        };
        target.addEventListener('pointermove', (event) => {
            if (drag || !canPoint() || event.pointerType === 'touch') {
                return;
            }

            pointerX = event.clientX;
            if (frame) {
                return;
            }

            frame = requestAnimationFrame(() => {
                letters.forEach(({ letter }) => {
                    const bounds = letter.getBoundingClientRect();
                    const distance = Math.abs(pointerX - (bounds.left + bounds.width / 2));
                    letter.style.setProperty('--tech-proximity', Math.max(0, 1 - distance / 105).toFixed(3));
                });
                frame = 0;
            });
        }, { passive: true });
        target.addEventListener('pointerleave', () => {
            if (!drag) {
                reset();
            }
        });
        target.addEventListener('pointercancel', reset);
        reducedMotion.addEventListener('change', () => {
            finishDrag(null, true);
            releaseTimers.forEach((timer, letter) => {
                clearTimeout(timer);
                letter.classList.remove('is-returning');
            });
            releaseTimers.clear();
            reset();
        });
        finePointer.addEventListener('change', reset);
    });

    site.querySelectorAll('[data-rotating]').forEach((root) => {
        const words = Array.from(root.querySelectorAll('[data-rotating-word]'));
        const toggle = root.querySelector('[data-rotation-toggle]');
        if (words.length < 2 || !(toggle instanceof HTMLButtonElement) || typeof Element.prototype.animate !== 'function') {
            return;
        }

        words.forEach((word) => {
            const characters = Array.from(word.textContent).map((character) => {
                const span = document.createElement('span');
                span.className = 'rotating-character';
                span.textContent = character === ' ' ? '\u00a0' : character;
                return span;
            });
            word.replaceChildren(...characters);
        });

        let index = 0;
        let timer = 0;
        let visible = false;
        let paused = false;
        let generation = 0;
        const animations = new Set();
        const eligible = () => visible && !paused && !reducedMotion.matches && document.visibilityState !== 'hidden';
        const settle = () => words.forEach((word, position) => word.classList.toggle('is-active', position === index));
        const stop = () => {
            generation += 1;
            clearTimeout(timer);
            animations.forEach((animation) => animation.cancel());
            animations.clear();
            settle();
        };

        const schedule = () => {
            clearTimeout(timer);
            if (eligible()) {
                timer = setTimeout(rotate, 3600);
            }
        };

        const rotate = async () => {
            if (!eligible()) {
                return;
            }

            const previous = words[index];
            index = (index + 1) % words.length;
            const next = words[index];
            const turn = ++generation;
            next.classList.add('is-active');
            const animateWord = (word, incoming) => {
                Array.from(word.children).forEach((character, position) => {
                    const animation = character.animate(incoming ? [
                        { opacity: 0, transform: 'translateY(85%) rotateX(-70deg)' },
                        { opacity: 1, transform: 'translateY(0) rotateX(0)' },
                    ] : [
                        { opacity: 1, transform: 'translateY(0) rotateX(0)' },
                        { opacity: 0, transform: 'translateY(-85%) rotateX(70deg)' },
                    ], {
                        duration: incoming ? 430 : 270,
                        delay: position * 15,
                        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                        fill: 'both',
                    });
                    animations.add(animation);
                });
            };

            animateWord(previous, false);
            animateWord(next, true);
            await Promise.allSettled(Array.from(animations, (animation) => animation.finished));
            if (turn !== generation) {
                return;
            }

            settle();
            animations.forEach((animation) => animation.cancel());
            animations.clear();
            schedule();
        };

        const sync = () => {
            stop();
            if (reducedMotion.matches) {
                index = 0;
                settle();
            }
            toggle.hidden = reducedMotion.matches;
            schedule();
        };

        toggle.addEventListener('click', () => {
            paused = !paused;
            toggle.setAttribute('aria-pressed', String(paused));
            toggle.setAttribute('aria-label', paused ? 'Lanjutkan pergantian keahlian' : 'Jeda pergantian keahlian');
            toggle.querySelector('[data-rotation-pause]').toggleAttribute('hidden', paused);
            toggle.querySelector('[data-rotation-play]').toggleAttribute('hidden', !paused);
            sync();
        });
        new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            sync();
        }).observe(root);
        document.addEventListener('visibilitychange', sync);
        reducedMotion.addEventListener('change', sync);
        sync();
    });

    site.querySelectorAll('[data-magnet]').forEach((button) => {
        let frame = 0;
        let offsetX = 0;
        let offsetY = 0;
        const reset = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            button.style.removeProperty('--magnet-x');
            button.style.removeProperty('--magnet-y');
        };

        button.addEventListener('pointermove', (event) => {
            if (!canPoint() || event.pointerType === 'touch' || button.matches(':focus-visible')) {
                return;
            }

            const bounds = button.getBoundingClientRect();
            offsetX = Math.max(-4, Math.min(4, (event.clientX - bounds.left - bounds.width / 2) * 0.065));
            offsetY = Math.max(-3, Math.min(3, (event.clientY - bounds.top - bounds.height / 2) * 0.1));
            if (!frame) {
                frame = requestAnimationFrame(() => {
                    button.style.setProperty('--magnet-x', `${offsetX}px`);
                    button.style.setProperty('--magnet-y', `${offsetY}px`);
                    frame = 0;
                });
            }
        }, { passive: true });
        button.addEventListener('pointerleave', reset);
        button.addEventListener('pointercancel', reset);
        button.addEventListener('focus', reset);
        finePointer.addEventListener('change', reset);
        reducedMotion.addEventListener('change', reset);
    });
}
