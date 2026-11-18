document.querySelectorAll('[data-photo-crop]').forEach((form) => {
    const fileInput = form.querySelector('[data-crop-file]');
    const editor = form.querySelector('[data-crop-editor]');
    const stage = form.querySelector('[data-crop-stage]');
    const image = form.querySelector('[data-crop-image]');
    const saveButton = form.querySelector('[data-crop-save]');
    const existingInput = form.querySelector('[data-crop-existing]');
    const status = form.querySelector('[data-crop-status]');
    const error = form.querySelector('[data-crop-error]');
    const controls = Object.fromEntries(['x', 'y', 'zoom'].map((key) => [key, form.querySelector(`[data-crop-control="${key}"]`)]));
    const values = Object.fromEntries(['x', 'y', 'zoom'].map((key) => [key, form.querySelector(`[data-crop-value="${key}"]`)]));
    let state = { x: 0.5, y: 0.5, zoom: 1 };
    let ready = false;
    let loading = false;
    let loadId = 0;
    let objectUrl;
    let drag;
    let pinch;
    const pointers = new Map();
    let overflow = { x: 0, y: 0 };
    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    const render = () => {
        if (!ready || stage.clientWidth === 0) {
            return;
        }

        const size = stage.clientWidth * 0.8;
        const scale = size / Math.min(image.naturalWidth, image.naturalHeight) * state.zoom;
        const width = image.naturalWidth * scale;
        const height = image.naturalHeight * scale;
        overflow = { x: width - size, y: height - size };
        Object.assign(image.style, {
            width: `${width}px`, height: `${height}px`,
            left: `${stage.clientWidth * 0.1 - overflow.x * state.x}px`,
            top: `${stage.clientHeight * 0.1 - overflow.y * state.y}px`,
        });

        for (const key of ['x', 'y', 'zoom']) {
            values[key].value = state[key].toFixed(6);
            controls[key].value = key === 'zoom' ? state[key] : state[key] * 100;
        }
        controls.x.disabled = overflow.x < 0.5;
        controls.y.disabled = overflow.y < 0.5;
        form.querySelector('[data-crop-zoom]').textContent = `${state.zoom.toFixed(2)}×`;
    };

    const setZoom = (zoom) => {
        const previousSize = Math.min(image.naturalWidth, image.naturalHeight) / state.zoom;
        const centerX = previousSize / 2 + (image.naturalWidth - previousSize) * state.x;
        const centerY = previousSize / 2 + (image.naturalHeight - previousSize) * state.y;
        state.zoom = clamp(zoom, 1, 3);
        const nextSize = Math.min(image.naturalWidth, image.naturalHeight) / state.zoom;
        state.x = image.naturalWidth > nextSize ? clamp((centerX - nextSize / 2) / (image.naturalWidth - nextSize), 0, 1) : 0.5;
        state.y = image.naturalHeight > nextSize ? clamp((centerY - nextSize / 2) / (image.naturalHeight - nextSize), 0, 1) : 0.5;
        render();
    };

    const showError = (message) => {
        error.textContent = message;
        error.hidden = false;
    };

    const loadImage = async (source, settings, existing) => {
        const currentLoad = ++loadId;
        loading = true;
        ready = false;
        drag = null;
        pinch = null;
        pointers.clear();
        saveButton.disabled = true;
        editor.hidden = true;
        error.hidden = true;
        Object.values(values).forEach((input) => { input.disabled = true; });
        status.textContent = 'Menyiapkan preview foto…';
        image.src = source;

        try {
            await image.decode();
            if (currentLoad !== loadId) {
                return;
            }
            if (Math.max(image.naturalWidth, image.naturalHeight) > 4096) {
                throw new Error('Ukuran foto maksimal 4096 × 4096 piksel. Pilih versi yang lebih kecil.');
            }

            state = {
                x: clamp(Number.isFinite(Number(settings.x)) ? Number(settings.x) : 0.5, 0, 1),
                y: clamp(Number.isFinite(Number(settings.y)) ? Number(settings.y) : 0.5, 0, 1),
                zoom: clamp(Number.isFinite(Number(settings.zoom)) ? Number(settings.zoom) : 1, 1, 3),
            };
            existingInput.value = existing ? '1' : '0';
            fileInput.required = !existing;
            ready = true;
            editor.hidden = false;
            Object.values(values).forEach((input) => { input.disabled = false; });
            status.textContent = 'Preview siap. Atur posisi dan zoom, lalu simpan foto.';
            render();
        } catch (failure) {
            if (currentLoad !== loadId) {
                return;
            }
            existingInput.value = '0';
            fileInput.required = true;
            status.textContent = 'Preview belum siap.';
            showError(failure instanceof Error && failure.message.includes('4096') ? failure.message : 'Foto tidak dapat dibuka. Pilih gambar JPEG, PNG, atau WebP lainnya.');
        } finally {
            if (currentLoad === loadId) {
                loading = false;
                saveButton.disabled = !ready;
            }
        }
    };

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        ++loadId;
        ready = false;
        existingInput.value = '0';
        fileInput.required = true;
        editor.hidden = true;
        Object.values(values).forEach((input) => { input.disabled = true; });
        error.hidden = true;
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = undefined;
        }
        if (!file) {
            loading = false;
            saveButton.disabled = false;
            status.textContent = 'Pilih foto untuk mengatur bagian yang tampil.';
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            loading = false;
            saveButton.disabled = true;
            showError('Pilih gambar JPEG, PNG, atau WebP berukuran maksimal 5 MB.');
            return;
        }
        objectUrl = URL.createObjectURL(file);
        loadImage(objectUrl, {}, false);
    });

    form.querySelector('[data-crop-current]')?.addEventListener('click', () => {
        fileInput.value = '';
        let settings = {};
        try {
            settings = JSON.parse(form.dataset.cropSettings);
        } catch {
            settings = {};
        }
        loadImage(form.dataset.originalUrl, settings, true);
    });

    for (const key of ['x', 'y', 'zoom']) {
        controls[key].addEventListener('input', () => {
            if (!ready) {
                return;
            }
            if (key === 'zoom') {
                setZoom(Number(controls.zoom.value));
            } else {
                state[key] = clamp(Number(controls[key].value) / 100, 0, 1);
                render();
            }
        });
    }

    form.querySelector('[data-crop-reset]').addEventListener('click', () => {
        state = { x: 0.5, y: 0.5, zoom: 1 };
        render();
    });

    stage.addEventListener('pointerdown', (event) => {
        if (!ready || event.button !== 0) {
            return;
        }
        event.preventDefault();
        stage.focus({ preventScroll: true });
        stage.setPointerCapture(event.pointerId);
        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        if (pointers.size === 2) {
            const [first, second] = Array.from(pointers.values());
            pinch = { distance: Math.hypot(first.x - second.x, first.y - second.y), zoom: state.zoom };
            drag = null;
        } else if (pointers.size === 1) {
            drag = { id: event.pointerId, x: event.clientX, y: event.clientY };
        }
    });
    stage.addEventListener('pointermove', (event) => {
        if (!pointers.has(event.pointerId)) {
            return;
        }
        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        if (pinch && pointers.size === 2 && pinch.distance > 0) {
            const [first, second] = Array.from(pointers.values());
            setZoom(pinch.zoom * Math.hypot(first.x - second.x, first.y - second.y) / pinch.distance);
            return;
        }
        if (!drag || drag.id !== event.pointerId) {
            return;
        }
        if (overflow.x > 0.5) {
            state.x = clamp(state.x - (event.clientX - drag.x) / overflow.x, 0, 1);
        }
        if (overflow.y > 0.5) {
            state.y = clamp(state.y - (event.clientY - drag.y) / overflow.y, 0, 1);
        }
        drag.x = event.clientX;
        drag.y = event.clientY;
        render();
    });
    const endDrag = (event) => {
        pointers.delete(event.pointerId);
        pinch = null;
        const remaining = Array.from(pointers.entries())[0];
        drag = remaining ? { id: remaining[0], ...remaining[1] } : null;
    };
    stage.addEventListener('pointerup', endDrag);
    stage.addEventListener('pointercancel', endDrag);
    stage.addEventListener('lostpointercapture', endDrag);
    stage.addEventListener('keydown', (event) => {
        if (!ready || !['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', '+', '=', '-'].includes(event.key)) {
            return;
        }
        event.preventDefault();
        if (event.key === '+' || event.key === '=' || event.key === '-') {
            setZoom(state.zoom + (event.key === '-' ? -0.1 : 0.1));
        } else {
            const key = event.key === 'ArrowLeft' || event.key === 'ArrowRight' ? 'x' : 'y';
            const direction = event.key === 'ArrowLeft' || event.key === 'ArrowUp' ? -1 : 1;
            state[key] = clamp(state[key] + direction * 0.02, 0, 1);
            render();
        }
    });

    if ('ResizeObserver' in window) {
        new ResizeObserver(render).observe(stage);
    } else {
        window.addEventListener('resize', render);
    }
    form.addEventListener('submit', (event) => {
        if (loading || (!ready && fileInput.files.length > 0)) {
            event.preventDefault();
            showError('Tunggu preview foto siap sebelum menyimpan.');
            return;
        }
        saveButton.disabled = true;
        saveButton.textContent = 'Menyimpan foto…';
    });
    window.addEventListener('pagehide', () => {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
        }
    });
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            saveButton.disabled = loading || (!ready && fileInput.files.length > 0);
            saveButton.textContent = 'Simpan foto';
        }
    });
});
