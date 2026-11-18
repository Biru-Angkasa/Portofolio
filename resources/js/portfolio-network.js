const networkSections = document.querySelectorAll('.portfolio-site [data-network-background]');

if (networkSections.length) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

    networkSections.forEach((section) => {
        const canvas = document.createElement('canvas');
        canvas.className = 'portfolio-network';
        canvas.setAttribute('aria-hidden', 'true');
        canvas.hidden = true;
        const context = canvas.getContext('2d');
        if (!context) {
            return;
        }
        section.prepend(canvas);

        let width = 0;
        let height = 0;
        let points = [];
        let visible = false;
        let frame = 0;
        let last = 0;
        let energy = 0;
        let targetEnergy = 0;
        let pointerX = 0;
        let pointerY = 0;
        let targetX = 0;
        let targetY = 0;
        let ripple = null;
        const variant = section.dataset.networkBackground;

        const resize = () => {
            const bounds = section.getBoundingClientRect();
            const nextWidth = Math.round(bounds.width);
            const nextHeight = Math.round(bounds.height);
            const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
            if (width === nextWidth && height === nextHeight && canvas.width === Math.round(width * dpr)) {
                return;
            }

            width = nextWidth;
            height = nextHeight;
            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            context.setTransform(dpr, 0, 0, dpr, 0, 0);
            const spacing = width < 600 ? 56 : 48;
            points = [];
            for (let row = 0; row * spacing < height; row += 1) {
                for (let column = 0; column * spacing < width; column += 1) {
                    points.push({
                        x: column * spacing + spacing / 2 + (variant === 'nodes' ? Math.sin(row * 3 + column) * 12 : 0),
                        y: row * spacing + spacing / 2 + (variant === 'nodes' ? Math.cos(column * 2 + row) * 12 : 0),
                    });
                }
            }
        };

        const paint = (now) => {
            frame = requestAnimationFrame(paint);
            if (now - last < 1000 / 30) {
                return;
            }
            const delta = Math.min(now - last || 33, 64);
            last = now;
            const ease = 1 - Math.exp(-delta / 140);
            pointerX += (targetX - pointerX) * ease;
            pointerY += (targetY - pointerY) * ease;
            energy += (targetEnergy - energy) * ease;
            context.clearRect(0, 0, width, height);

            const glowX = pointerX * energy + (width * 0.75 + Math.sin(now / 9000) * 36) * (1 - energy);
            const glowY = pointerY * energy + height * 0.45 * (1 - energy);
            const glow = context.createRadialGradient(glowX, glowY, 0, glowX, glowY, 230);
            glow.addColorStop(0, `rgba(104, 166, 237, ${0.035 + energy * 0.055})`);
            glow.addColorStop(1, 'rgba(104, 166, 237, 0)');
            context.fillStyle = glow;
            context.fillRect(0, 0, width, height);
            const nearby = [];

            context.beginPath();
            points.forEach((point) => {
                context.moveTo(point.x + 0.8, point.y);
                context.arc(point.x, point.y, 0.8, 0, Math.PI * 2);
                const dx = point.x - glowX;
                const dy = point.y - glowY;
                if (dx * dx + dy * dy < 160 * 160) {
                    nearby.push(point);
                }
            });
            context.fillStyle = 'rgba(23, 62, 112, 0.13)';
            context.fill();

            nearby.forEach((point, index) => {
                const distance = Math.hypot(point.x - glowX, point.y - glowY);
                const intensity = (1 - distance / 160) * (0.3 + energy * 0.7);
                context.beginPath();
                context.arc(point.x, point.y, 1.1 + intensity * 1.6, 0, Math.PI * 2);
                context.fillStyle = `rgba(41, 99, 169, ${0.18 + intensity * 0.45})`;
                context.fill();

                nearby.slice(index + 1).forEach((other) => {
                    if (Math.hypot(other.x - point.x, other.y - point.y) > 74) {
                        return;
                    }
                    context.beginPath();
                    context.moveTo(point.x, point.y);
                    context.lineTo(other.x, other.y);
                    context.strokeStyle = `rgba(45, 107, 182, ${intensity * 0.18})`;
                    context.lineWidth = 0.7;
                    context.stroke();
                });
            });

            if (ripple) {
                const age = (now - ripple.started) / 1200;
                if (age >= 1) {
                    ripple = null;
                } else {
                    context.beginPath();
                    context.arc(ripple.x, ripple.y, 12 + age * 180, 0, Math.PI * 2);
                    context.strokeStyle = `rgba(45, 107, 182, ${(1 - age) * 0.28})`;
                    context.lineWidth = 1;
                    context.stroke();
                }
            }
        };

        const sync = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            last = 0;
            canvas.hidden = reducedMotion.matches;
            section.toggleAttribute('data-network-ready', !reducedMotion.matches);
            if (visible && !reducedMotion.matches && document.visibilityState !== 'hidden') {
                resize();
                frame = requestAnimationFrame(paint);
            }
        };

        const pointAt = (event) => {
            const bounds = section.getBoundingClientRect();
            targetX = event.clientX - bounds.left;
            targetY = event.clientY - bounds.top;
        };
        section.addEventListener('pointermove', (event) => {
            if (reducedMotion.matches || !finePointer.matches || event.pointerType === 'touch') {
                return;
            }
            pointAt(event);
            targetEnergy = 1;
        }, { passive: true });
        section.addEventListener('pointerleave', () => { targetEnergy = 0; });
        section.addEventListener('pointercancel', () => { targetEnergy = 0; });
        section.addEventListener('pointerdown', (event) => {
            if (reducedMotion.matches) {
                return;
            }
            pointAt(event);
            pointerX = targetX;
            pointerY = targetY;
            energy = 1;
            ripple = { x: targetX, y: targetY, started: performance.now() };
        }, { passive: true });
        new ResizeObserver(() => {
            if (visible) {
                resize();
            }
        }).observe(section);
        new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            sync();
        }).observe(section);
        document.addEventListener('visibilitychange', sync);
        reducedMotion.addEventListener('change', sync);
    });
}
