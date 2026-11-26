import './profile-photo-crop';
import './portfolio-threads';
import './portfolio-interactions';
import './portfolio-network';
import './portfolio-scroll';
import './portfolio-motion';

const toggle = document.querySelector('[data-menu-toggle]');
const nav = document.querySelector('[data-menu]');

if (toggle instanceof HTMLButtonElement && nav instanceof HTMLElement) {
    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        nav.classList.toggle('is-open', open);
    };

    toggle.addEventListener('click', () => {
        setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    nav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 63.99rem)').matches) {
                setOpen(false);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
}

const syncSocialContact = (row) => {
    const platform = row?.querySelector('select[name$="[platform]"]');
    const field = row?.querySelector('[data-social-contact]');
    const input = field?.querySelector('input');
    const label = field?.querySelector('[data-social-contact-label]');
    const help = field?.querySelector('[data-social-contact-help]');

    if (!(platform instanceof HTMLSelectElement) || !(input instanceof HTMLInputElement)) {
        return;
    }

    const isWhatsApp = platform.value === 'whatsapp';
    const isAutomatic = platform.value === 'auto';
    input.type = isWhatsApp ? 'tel' : (isAutomatic ? 'text' : 'url');
    input.inputMode = isWhatsApp ? 'tel' : (isAutomatic ? 'text' : 'url');
    input.placeholder = isWhatsApp ? '081234567890 atau +6281234567890' : (isAutomatic ? 'https://… atau 081234567890' : 'https://…');
    if (label) {
        label.textContent = isWhatsApp ? 'Nomor WhatsApp' : (isAutomatic ? 'Tautan akun / nomor WhatsApp' : 'Tautan profil');
    }
    if (help) {
        help.textContent = isWhatsApp
            ? 'Cukup isi nomor. Awalan 08 otomatis diubah menjadi +62.'
            : (isAutomatic ? 'Isi tautan profil atau nomor WhatsApp; ikonnya dipilih otomatis.' : 'Isi tautan profil lengkap, misalnya https://instagram.com/akun.');
    }
};

document.querySelectorAll('[data-row]').forEach(syncSocialContact);
document.addEventListener('change', (event) => {
    if (event.target instanceof HTMLSelectElement && event.target.matches('[name$="[platform]"]')) {
        syncSocialContact(event.target.closest('[data-row]'));
    }
});

document.querySelectorAll('[data-repeater]').forEach((repeater) => {
    const rows = repeater.querySelector('[data-rows]');
    const template = repeater.querySelector('[data-row-template]');
    const empty = repeater.querySelector('[data-empty]');
    const addButton = repeater.querySelector('[data-add-row]');

    if (!(rows instanceof HTMLElement) || !(template instanceof HTMLTemplateElement)) {
        return;
    }

    let nextIndex = Math.max(-1, ...Array.from(rows.children, (row) => Number(row.dataset.index))) + 1;
    const updateEmpty = () => {
        if (empty instanceof HTMLElement) {
            empty.hidden = rows.children.length !== 0;
        }
        if (addButton instanceof HTMLButtonElement) {
            addButton.disabled = rows.children.length >= 30;
        }
    };

    addButton?.addEventListener('click', () => {
        if (rows.children.length >= 30) {
            return;
        }
        const fragment = template.content.cloneNode(true);
        fragment.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace('__INDEX__', String(nextIndex));
        });
        fragment.querySelector('[data-row]').dataset.index = String(nextIndex++);
        rows.append(fragment);
        syncSocialContact(rows.lastElementChild);
        updateEmpty();
        rows.lastElementChild?.querySelector('input, textarea')?.focus();
    });

    rows.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        const row = button?.closest('[data-row]');
        if (!row) {
            return;
        }
        if (button.hasAttribute('data-clear-logo')) {
            row.querySelector('input[name$="[logo_path]"]').value = '';
            row.querySelector('input[type="file"][name$="[logo]"]').value = '';
            const preview = row.querySelector('[data-logo-preview]');

            if (preview) {
                preview.hidden = true;
            }
        } else if (button.hasAttribute('data-remove-row')) {
            row.remove();
            updateEmpty();
            addButton?.focus();
        } else if (button.dataset.moveRow === 'up' && row.previousElementSibling) {
            rows.insertBefore(row, row.previousElementSibling);
            button.focus();
        } else if (button.dataset.moveRow === 'down' && row.nextElementSibling) {
            rows.insertBefore(row.nextElementSibling, row);
            button.focus();
        }
    });

    updateEmpty();
});

document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.confirmDelete || 'Hapus data ini?';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

const copyEmailStatus = document.querySelector('[data-copy-email-status]');

document.querySelectorAll('[data-copy-email]').forEach((button) => {
    const defaultLabel = button.textContent.trim();
    let timerId = 0;

    button.addEventListener('click', async () => {
        const email = button.getAttribute('data-copy-email') || '';

        if (!email) {
            return;
        }

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(email);
            } else {
                const helper = document.createElement('textarea');
                helper.value = email;
                helper.setAttribute('readonly', '');
                helper.style.position = 'fixed';
                helper.style.opacity = '0';
                document.body.append(helper);
                helper.select();
                document.execCommand('copy');
                helper.remove();
            }

            button.textContent = 'Email disalin';
            if (copyEmailStatus) {
                copyEmailStatus.textContent = `Alamat email ${email} disalin ke papan klip.`;
            }

            window.clearTimeout(timerId);
            timerId = window.setTimeout(() => {
                button.textContent = defaultLabel;
            }, 2200);
        } catch {
            if (copyEmailStatus) {
                copyEmailStatus.textContent = `Salin manual alamat email: ${email}`;
            }
        }
    });
});

const lightboxDialog = document.querySelector('[data-lightbox-dialog]');

if (lightboxDialog instanceof HTMLDialogElement) {
    const lightboxImage = lightboxDialog.querySelector('[data-lightbox-image]');
    const lightboxTitle = lightboxDialog.querySelector('[data-lightbox-title]');
    const lightboxDesc = lightboxDialog.querySelector('[data-lightbox-desc]');
    const closeButton = lightboxDialog.querySelector('[data-lightbox-close]');

    document.querySelectorAll('[data-lightbox-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const src = trigger.getAttribute('data-lightbox-src') || '';
            const alt = trigger.getAttribute('data-lightbox-alt') || '';
            const caption = trigger.getAttribute('data-lightbox-caption') || '';

            if (!src || !(lightboxImage instanceof HTMLImageElement)) {
                return;
            }

            lightboxImage.src = src;
            lightboxImage.alt = alt;
            if (lightboxTitle) {
                lightboxTitle.textContent = alt;
            }
            if (lightboxDesc) {
                lightboxDesc.textContent = caption;
                lightboxDesc.hidden = caption === '';
            }

            lightboxDialog.showModal();
        });
    });

    closeButton?.addEventListener('click', () => lightboxDialog.close());
    lightboxDialog.addEventListener('click', (event) => {
        if (event.target === lightboxDialog) {
            lightboxDialog.close();
        }
    });
}

const portfolioSite = document.querySelector('.portfolio-site');

if (portfolioSite instanceof HTMLElement) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const precisePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const animations = new Set();
    const revealed = new WeakSet();
    let stopMotion = () => {};

    const syncMotion = () => {
        stopMotion();

        if (reducedMotion.matches) {
            return;
        }

        const cleanup = [];
        const revealGroups = portfolioSite.querySelectorAll(
            '#beranda > div > :first-child > *, #beranda > div > :last-child, #beranda aside > *, main > section:not(#beranda) > div > *',
        );
        const revealTargets = Array.from(revealGroups).flatMap((group) => {
            if (group.matches('ul, ol')) {
                return Array.from(group.children);
            }

            if (group.id === 'keahlian') {
                return Array.from(group.querySelectorAll(':scope > h3, :scope > dl > *'));
            }

            if (group.children.length && Array.from(group.children).every((child) => child.classList.contains('portfolio-card'))) {
                return Array.from(group.children);
            }

            return [group];
        }).filter((target) => !target.matches('[data-scroll-reveal], [data-horizontal-pin]') && !target.querySelector('[data-scroll-reveal]'));

        if ('IntersectionObserver' in window && typeof Element.prototype.animate === 'function') {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(({ target, isIntersecting, intersectionRatio }) => {
                    if (!isIntersecting || intersectionRatio < 0.15) {
                        return;
                    }

                    observer.unobserve(target);
                    revealed.add(target);
                    target.classList.remove('is-reveal-pending');

                    const siblings = Array.from(target.parentElement.children);
                    const isProject = target.matches('#bukti .portfolio-card, #blog .portfolio-card');
                    const row = siblings.filter((sibling) => sibling.offsetTop === target.offsetTop);
                    const animation = target.animate([
                        { opacity: 0, translate: isProject ? '0 32px' : '0 20px', scale: isProject ? '0.985' : '1' },
                        { opacity: 1, translate: '0 0', scale: '1' },
                    ], {
                        duration: 650,
                        delay: isProject ? Math.min(row.indexOf(target), 2) * 110 : Math.min(siblings.indexOf(target), 2) * 60,
                        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                        fill: 'backwards',
                    });

                    animations.add(animation);
                    animation.onfinish = () => animations.delete(animation);
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -80px 0px' });

            revealTargets.forEach((target) => {
                if (!revealed.has(target)) {
                    target.classList.add('is-reveal-pending');
                    observer.observe(target);
                }
            });

            const onFocus = (event) => {
                revealTargets.forEach((target) => {
                    if (target.contains(event.target)) {
                        observer.unobserve(target);
                        revealed.add(target);
                        target.classList.remove('is-reveal-pending');
                    }
                });
            };
            portfolioSite.addEventListener('focusin', onFocus);
            cleanup.push(() => {
                observer.disconnect();
                portfolioSite.removeEventListener('focusin', onFocus);
                revealTargets.forEach((target) => target.classList.remove('is-reveal-pending'));
            });
        }

        if (precisePointer.matches) {
            portfolioSite.querySelectorAll('#bukti .portfolio-card, #blog .portfolio-card').forEach((card) => {
                card.setAttribute('data-depth', '');
                card.setAttribute('data-spotlight', '');
            });

            portfolioSite.querySelectorAll('[data-depth]').forEach((surface) => {
                let bounds;
                let frame = 0;
                let rotationX = 0;
                let rotationY = 0;
                const strength = surface.classList.contains('portfolio-portrait') ? 2.5 : 1.25;

                const reset = () => {
                    cancelAnimationFrame(frame);
                    frame = 0;
                    bounds = null;
                    surface.style.removeProperty('--depth-x');
                    surface.style.removeProperty('--depth-y');
                    surface.style.removeProperty('--spotlight-x');
                    surface.style.removeProperty('--spotlight-y');
                };
                const onEnter = () => {
                    bounds = surface.getBoundingClientRect();
                };
                const onMove = (event) => {
                    if (!bounds || event.pointerType === 'touch') {
                        return;
                    }

                    const x = Math.max(-1, Math.min(1, ((event.clientX - bounds.left) / bounds.width - 0.5) * 2));
                    const y = Math.max(-1, Math.min(1, ((event.clientY - bounds.top) / bounds.height - 0.5) * 2));
                    rotationX = -y * strength;
                    rotationY = x * strength;

                    if (!frame) {
                        frame = requestAnimationFrame(() => {
                            surface.style.setProperty('--depth-x', `${rotationX.toFixed(2)}deg`);
                            surface.style.setProperty('--depth-y', `${rotationY.toFixed(2)}deg`);
                            surface.style.setProperty('--spotlight-x', `${((x + 1) * 50).toFixed(1)}%`);
                            surface.style.setProperty('--spotlight-y', `${((y + 1) * 50).toFixed(1)}%`);
                            frame = 0;
                        });
                    }
                };

                surface.addEventListener('pointerenter', onEnter);
                surface.addEventListener('pointermove', onMove);
                surface.addEventListener('pointerleave', reset);
                surface.addEventListener('pointercancel', reset);
                cleanup.push(() => {
                    reset();
                    surface.removeEventListener('pointerenter', onEnter);
                    surface.removeEventListener('pointermove', onMove);
                    surface.removeEventListener('pointerleave', reset);
                    surface.removeEventListener('pointercancel', reset);
                });
            });
        }

        stopMotion = () => {
            cleanup.forEach((stop) => stop());
            animations.forEach((animation) => animation.cancel());
            animations.clear();
        };
    };

    reducedMotion.addEventListener('change', syncMotion);
    precisePointer.addEventListener('change', syncMotion);
    syncMotion();
}
