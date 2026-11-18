const section = document.querySelector('#beranda');
const canvas = section?.querySelector('canvas[data-threads]');
const still = section?.querySelector('[data-threads-still]');

if (section instanceof HTMLElement && canvas instanceof HTMLCanvasElement && still instanceof SVGElement && canvas.dataset.bound !== 'true') {
    canvas.dataset.bound = 'true';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const context = canvas.getContext('2d');
    const fiber = '#2a5c9e';
    const pointer = { x: 0, y: 0, targetX: 0, targetY: 0, energy: 0, targetEnergy: 0 };
    let lines = [];
    let width = 0;
    let height = 0;
    let running = false;
    let visible = false;
    let frame = 0;
    let last = 0;
    let elapsed = 0;

    const lineCount = (simple) => {
        if (simple) {
            return width < 700 ? 6 : 8;
        }

        if (width < 420) {
            return 6;
        }

        if (width < 800) {
            return 8;
        }

        if (width < 1200) {
            return 10;
        }

        return 12;
    };

    const resize = () => {
        if (context === null) {
            return;
        }

        const rect = section.getBoundingClientRect();
        const nextWidth = Math.max(1, rect.width);
        const nextHeight = Math.max(1, rect.height);
        const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
        const pixelWidth = Math.round(nextWidth * dpr);
        const pixelHeight = Math.round(nextHeight * dpr);

        if (canvas.width !== pixelWidth || canvas.height !== pixelHeight) {
            canvas.width = pixelWidth;
            canvas.height = pixelHeight;
        }

        context.setTransform(dpr, 0, 0, dpr, 0, 0);
        width = nextWidth;
        height = nextHeight;

        const simple = !finePointer.matches;
        const count = lineCount(simple);
        const start = height * 0.28;
        const end = height * 0.52;
        const scale = Math.min(1, Math.max(0.72, width / 1100));
        lines = Array.from({ length: count }, (_, index) => {
            const t = count === 1 ? 0.5 : index / (count - 1);

            return {
                y: start + (end - start) * t + Math.sin(index * 1.7) * height * 0.018,
                phase: index * 0.1,
                freq: Math.PI * 2 / (width * 1.1),
                freq2: Math.PI * 2 / (width * 0.65),
                freq3: Math.PI / width,
                speed: simple ? 0.09 : 0.13,
                amp: Math.min(140, height * (simple ? 0.07 : 0.18)) * scale,
                amp2: simple ? 8 : 24 * scale,
                amp3: simple ? 0 : 14 * scale,
                width: 0.7 + (index % 3) * 0.12,
                alpha: 0.17 + (index % 4) * 0.025,
            };
        });
    };

    const bend = (x, y) => {
        if (pointer.energy < 0.004) {
            return 0;
        }

        const dx = x - pointer.x;
        const dy = y - pointer.y;
        const radius = Math.max(128, Math.min(188, Math.min(width, height) * 0.2));
        const falloff = Math.exp(-(dx * dx + dy * dy) / (2 * radius * radius));

        return (y < pointer.y ? -1 : 1) * falloff * 16 * pointer.energy;
    };

    const draw = (time) => {
        context.globalAlpha = 1;
        context.clearRect(0, 0, width, height);
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.strokeStyle = fiber;

        const step = width < 480 ? 16 : 10;

        lines.forEach((line) => {
            const points = Math.ceil(width / step);
            context.beginPath();

            for (let index = 0; index <= points; index += 1) {
                const x = Math.min(width, index * step);
                const wave = Math.sin(x * line.freq + time * line.speed + line.phase) * line.amp
                    + Math.sin(x * line.freq2 - time * line.speed * 0.41 + line.phase * 1.8) * line.amp2
                    + Math.sin(x * line.freq3 + time * line.speed * 0.23 + line.phase * 0.7) * line.amp3;
                const y = line.y + wave + bend(x, line.y);

                if (index === 0) {
                    context.moveTo(x, y);
                } else {
                    context.lineTo(x, y);
                }
            }

            context.globalAlpha = line.alpha * 0.28;
            context.lineWidth = line.width + 1.15;
            context.stroke();
            context.globalAlpha = line.alpha;
            context.lineWidth = line.width;
            context.stroke();
        });

        context.globalAlpha = 1;
    };

    const tick = (now) => {
        frame = requestAnimationFrame(tick);

        if (!last) {
            last = now;
        }

        elapsed += Math.min(32, now - last);
        last = now;
        pointer.x += (pointer.targetX - pointer.x) * 0.07;
        pointer.y += (pointer.targetY - pointer.y) * 0.07;
        const ease = pointer.targetEnergy > pointer.energy ? 0.045 : 0.016;
        pointer.energy += (pointer.targetEnergy - pointer.energy) * ease;
        draw(elapsed / 1000);
    };

    const stop = () => {
        cancelAnimationFrame(frame);
        frame = 0;
        running = false;
        last = 0;
    };

    const start = () => {
        if (running || context === null) {
            return;
        }

        canvas.hidden = false;
        still.setAttribute('hidden', '');
        resize();
        running = true;
        frame = requestAnimationFrame(tick);
    };

    const sync = () => {
        if (reducedMotion.matches || context === null) {
            stop();
            canvas.hidden = true;
            still.removeAttribute('hidden');

            return;
        }

        if (visible && document.visibilityState !== 'hidden') {
            start();

            return;
        }

        stop();
    };

    const onMove = (event) => {
        if (!finePointer.matches || (event.pointerType !== 'mouse' && event.pointerType !== 'pen')) {
            return;
        }

        const rect = section.getBoundingClientRect();
        pointer.targetX = event.clientX - rect.left;
        pointer.targetY = event.clientY - rect.top;
        pointer.targetEnergy = 1;
    };

    section.addEventListener('pointermove', onMove, { passive: true });
    section.addEventListener('pointerleave', () => {
        pointer.targetEnergy = 0;
    });
    finePointer.addEventListener('change', () => {
        pointer.targetEnergy = 0;
        resize();
    });
    reducedMotion.addEventListener('change', sync);
    document.addEventListener('visibilitychange', sync);
    new ResizeObserver(() => {
        resize();

        if (!running && !canvas.hidden) {
            draw(elapsed / 1000);
        }
    }).observe(section);
    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        sync();
    }).observe(section);
}
