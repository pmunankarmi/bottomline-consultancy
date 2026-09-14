(function () {
    'use strict';

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // --- Footer year ---
    const yearEl = document.getElementById('year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    // --- Header scroll state ---
    const header = document.getElementById('siteHeader');
    if (header) {
        const onScroll = () => {
            if (window.scrollY > 8) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // --- Mobile nav toggle ---
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    if (toggle && links) {
        toggle.addEventListener('click', () => {
            const open = links.classList.toggle('open');
            toggle.setAttribute('aria-expanded', String(open));
        });
        links.querySelectorAll('a').forEach((a) => {
            a.addEventListener('click', () => {
                links.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // --- Scroll reveal ---
    const revealables = document.querySelectorAll('.reveal');
    if (revealables.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        io.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
        );
        revealables.forEach((el) => io.observe(el));
    } else {
        revealables.forEach((el) => el.classList.add('visible'));
    }

    // --- Hero chart draw-on-load ---
    const heroChart = document.getElementById('heroChart');
    if (heroChart) {
        const line = heroChart.querySelector('.chart-line');
        let len = 0;
        if (line) {
            try { len = line.getTotalLength(); } catch (e) { len = 700; }
            line.style.strokeDasharray = len;
            line.style.strokeDashoffset = prefersReducedMotion ? 0 : len;
        }
        const trigger = () => {
            heroChart.querySelectorAll('.chart-dot, .chart-area').forEach((el) => {
                el.classList.add('draw');
            });
            if (line && !prefersReducedMotion) {
                requestAnimationFrame(() => { line.style.strokeDashoffset = 0; });
            } else if (line) {
                line.style.strokeDashoffset = 0;
            }
        };
        if (document.readyState === 'complete') {
            requestAnimationFrame(trigger);
        } else {
            window.addEventListener('load', () => requestAnimationFrame(trigger), { once: true });
        }
    }

    // --- Number count-up (used by hero slides) ---
    const animateNum = (el, duration = 1400) => {
        const target = parseFloat(el.getAttribute('data-target'));
        if (Number.isNaN(target)) return;
        const targetStr = el.getAttribute('data-target');
        const decimals = (targetStr.split('.')[1] || '').length;
        const useCommas = Math.abs(target) >= 1000;
        const format = (v) => {
            const rounded = Number(v.toFixed(decimals));
            return useCommas
                ? rounded.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })
                : rounded.toFixed(decimals);
        };
        if (prefersReducedMotion) { el.textContent = format(target); return; }
        el.classList.add('is-animating');
        const start = performance.now();
        const step = (now) => {
            const t = Math.max(0, Math.min((now - start) / duration, 1));
            const eased = 1 - Math.pow(1 - t, 3);
            el.textContent = format(target * eased);
            if (t < 1) requestAnimationFrame(step);
            else el.classList.remove('is-animating');
        };
        requestAnimationFrame(step);
    };
    const animateSlideNumbers = (slide) => {
        slide.querySelectorAll('.num[data-target]').forEach((el, i) => {
            setTimeout(() => animateNum(el), i * 40);
        });
    };

    // --- Hero sliding banner ---
    const heroSlider = document.getElementById('heroSlider');
    if (heroSlider && heroSlider.querySelectorAll('.hero-slide').length) {
        const track = heroSlider.querySelector('.hero-track');
        const slides = heroSlider.querySelectorAll('.hero-slide');
        const dots = heroSlider.querySelectorAll('.hero-dot');
        const prevBtn = heroSlider.querySelector('.hero-prev');
        const nextBtn = heroSlider.querySelector('.hero-next');
        let idx = 0;
        let autoTimer = null;
        let paused = false;
        const pauseButton = heroSlider.querySelector('.hero-pause');
        const total = slides.length;

        const go = (n) => {
            idx = ((n % total) + total) % total;
            track.style.transform = `translateX(-${idx * 100}%)`;
            slides.forEach((s, i) => { s.classList.toggle('is-active', i === idx); s.inert = i !== idx; s.setAttribute('aria-hidden', String(i !== idx)); });
            dots.forEach((d, i) => d.classList.toggle('is-active', i === idx));
            animateSlideNumbers(slides[idx]);
        };
        const next = () => go(idx + 1);
        const prev = () => go(idx - 1);
        const startAuto = () => {
            if (prefersReducedMotion || paused || total < 2 || document.hidden) return;
            stopAuto();
            autoTimer = setInterval(next, 5000);
        };
        const stopAuto = () => { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } };

        dots.forEach((d) => d.addEventListener('click', () => {
            go(parseInt(d.getAttribute('data-target'), 10) || 0);
            startAuto();
        }));
        if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAuto(); });
        heroSlider.addEventListener('mouseenter', stopAuto);
        heroSlider.addEventListener('mouseleave', startAuto);

        // Touch swipe
        let touchStartX = null;
        heroSlider.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            stopAuto();
        }, { passive: true });
        heroSlider.addEventListener('touchend', (e) => {
            if (touchStartX == null) return;
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 40) { dx < 0 ? next() : prev(); }
            touchStartX = null;
            startAuto();
        });

        go(0);
        if (pauseButton) pauseButton.addEventListener('click', () => { paused = !paused; pauseButton.setAttribute('aria-pressed', String(paused)); paused ? stopAuto() : startAuto(); });
        heroSlider.addEventListener('focusin', stopAuto);
        heroSlider.addEventListener('focusout', (e) => { if (!heroSlider.contains(e.relatedTarget)) startAuto(); });
        document.addEventListener('visibilitychange', () => { document.hidden ? stopAuto() : startAuto(); });
        animateSlideNumbers(slides[0]);
        // Replay numbers on card hover for tactile feel
        slides.forEach((slide) => {
            const card = slide.querySelector('.hero-dash');
            if (!card) return;
            card.addEventListener('mouseenter', () => {
                if (slide.classList.contains('is-active')) animateSlideNumbers(slide);
            });
            // Per-tile replay on slide 2 growth grid
            slide.querySelectorAll('.gg-tile').forEach((tile) => {
                tile.addEventListener('mouseenter', () => {
                    tile.querySelectorAll('.num[data-target]').forEach((el) => animateNum(el, 900));
                });
            });
            // Per-row replay on P&L table
            slide.querySelectorAll('.pl-mini tr').forEach((row) => {
                row.addEventListener('mouseenter', () => {
                    row.querySelectorAll('.num[data-target]').forEach((el) => animateNum(el, 700));
                });
            });
        });
        startAuto();
    }

    // --- Hero dashboard KPI count-up ---
    const kpiNums = document.querySelectorAll('.kpi-num');
    if (kpiNums.length) {
        const animateKpi = (el) => {
            const target = parseFloat(el.getAttribute('data-kpi'));
            if (Number.isNaN(target)) return;
            if (prefersReducedMotion) {
                el.textContent = target.toFixed(2);
                return;
            }
            const dur = 1600;
            const start = performance.now();
            const step = (now) => {
                const t = Math.max(0, Math.min((now - start) / dur, 1));
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = (target * eased).toFixed(2);
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };
        kpiNums.forEach((el) => setTimeout(() => animateKpi(el), 300));
    }

    // --- Animated counters (count up on scroll into view) ---
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const animateCount = (el) => {
            const target = parseInt(el.getAttribute('data-count'), 10);
            if (Number.isNaN(target)) return;
            if (prefersReducedMotion) {
                el.textContent = target.toLocaleString();
                return;
            }
            const duration = 1400;
            const start = performance.now();
            const startVal = 0;
            const step = (now) => {
                const elapsed = now - start;
                const t = Math.max(0, Math.min(elapsed / duration, 1));
                const eased = 1 - Math.pow(1 - t, 3);
                const current = Math.round(startVal + (target - startVal) * eased);
                el.textContent = current.toLocaleString();
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };

        const countIo = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateCount(entry.target);
                        countIo.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.4 }
        );
        counters.forEach((el) => countIo.observe(el));
    }

    // --- Marquee duplication for seamless loop ---
    const marquee = document.getElementById('marqueeTrack');
    if (marquee) {
        const clone = marquee.cloneNode(true);
        clone.removeAttribute('id');
        clone.setAttribute('aria-hidden', 'true');
        // Combine: track contains original + clone
        const originalChildren = Array.from(marquee.children);
        const fragment = document.createDocumentFragment();
        originalChildren.forEach((child) => fragment.appendChild(child.cloneNode(true)));
        marquee.appendChild(fragment);
    }

    // --- Map pin / office sync (stylized regional map) ---
    const pins = document.querySelectorAll('.map-pin');
    const offices = document.querySelectorAll('.map-office');
    const setActive = (city) => {
        pins.forEach((p) => p.classList.toggle('active', p.dataset.city === city));
        offices.forEach((o) => o.classList.toggle('active', o.dataset.city === city));
    };
    const clearActive = () => {
        pins.forEach((p) => p.classList.remove('active'));
        offices.forEach((o) => o.classList.remove('active'));
    };
    pins.forEach((p) => {
        p.addEventListener('mouseenter', () => setActive(p.dataset.city));
        p.addEventListener('mouseleave', clearActive);
        p.addEventListener('focus', () => setActive(p.dataset.city));
        p.addEventListener('blur', clearActive);
        p.setAttribute('tabindex', '0');
    });
    offices.forEach((o) => {
        o.addEventListener('mouseenter', () => setActive(o.dataset.city));
        o.addEventListener('mouseleave', clearActive);
    });

    // --- Image-based regional map: wire pin overlays to country cards ---
    const imgMapPins = document.querySelectorAll('.img-map-pin');
    if (imgMapPins.length) {
        const cards = document.querySelectorAll('.country-card');
        const branchRows = document.querySelectorAll('.country-branch');

        const setActiveCity = (city) => {
            imgMapPins.forEach((p) => p.classList.toggle('active', p.dataset.city === city));
        };
        const setActiveCountry = (slug) => {
            cards.forEach((c) => c.classList.toggle('active', c.dataset.country === slug));
            imgMapPins.forEach((p) => p.classList.toggle('active', p.dataset.country === slug));
        };
        const clearAll = () => {
            imgMapPins.forEach((p) => p.classList.remove('active'));
            cards.forEach((c) => c.classList.remove('active'));
        };

        imgMapPins.forEach((pin) => {
            pin.addEventListener('mouseenter', () => {
                setActiveCity(pin.dataset.city);
                cards.forEach((c) => c.classList.toggle('active', c.dataset.country === pin.dataset.country));
            });
            pin.addEventListener('mouseleave', clearAll);
            pin.addEventListener('focus', () => {
                setActiveCity(pin.dataset.city);
                cards.forEach((c) => c.classList.toggle('active', c.dataset.country === pin.dataset.country));
            });
            pin.addEventListener('blur', clearAll);
            pin.addEventListener('click', () => {
                const targetCard = document.querySelector(`.country-card[data-country="${pin.dataset.country}"]`);
                if (targetCard) {
                    const branchRow = targetCard.querySelector(`.country-branch[data-city="${pin.dataset.city}"]`);
                    const target = branchRow || targetCard;
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetCard.classList.add('active');
                    setTimeout(() => targetCard.classList.remove('active'), 1800);
                }
            });
        });

        // Hover on a country card → light up its matching pins
        cards.forEach((card) => {
            card.addEventListener('mouseenter', () => {
                setActiveCountry(card.dataset.country);
            });
            card.addEventListener('mouseleave', clearAll);
        });

        // Hover on a specific branch row → light up just that pin
        branchRows.forEach((row) => {
            row.addEventListener('mouseenter', () => setActiveCity(row.dataset.city));
            row.addEventListener('mouseleave', clearAll);
        });
    }

    // --- 3D tilt on cards ---
    if (!prefersReducedMotion && matchMedia('(hover: hover) and (pointer: fine)').matches) {
        const tiltSelectors = [
            '.bento-tile', '.team-card', '.industry-card', '.service-card',
            '.card', '.country-card', '.feature', '.target', '.stat',
            '.branch', '.sister-card'
        ];
        const tiltCards = document.querySelectorAll(tiltSelectors.join(','));
        tiltCards.forEach((card) => {
            card.classList.add('tilt-card');
            let raf = null;
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width;
                const y = (e.clientY - rect.top) / rect.height;
                const rotateY = (x - 0.5) * 8;   // -4 to +4 deg
                const rotateX = (0.5 - y) * 8;
                if (raf) cancelAnimationFrame(raf);
                raf = requestAnimationFrame(() => {
                    card.style.transform =
                        `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-4px)`;
                });
            });
            card.addEventListener('mouseleave', () => {
                if (raf) cancelAnimationFrame(raf);
                card.style.transform = '';
            });
        });

        // --- Hero dashboard card parallax ---
        const heroSection = document.querySelector('.hero');
        const dashCard = document.querySelector('.dash-card');
        if (heroSection && dashCard) {
            let dashRaf = null;
            heroSection.addEventListener('mousemove', (e) => {
                const rect = heroSection.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width - 0.5);
                const y = ((e.clientY - rect.top) / rect.height - 0.5);
                if (dashRaf) cancelAnimationFrame(dashRaf);
                dashRaf = requestAnimationFrame(() => {
                    dashCard.style.transform =
                        `perspective(1400px) rotateY(${(x * 8).toFixed(2)}deg) rotateX(${(-y * 6).toFixed(2)}deg) translate3d(${(x * 14).toFixed(1)}px, ${(y * 10).toFixed(1)}px, 0)`;
                });
            });
            heroSection.addEventListener('mouseleave', () => {
                if (dashRaf) cancelAnimationFrame(dashRaf);
                dashCard.style.transform = '';
            });
        }

        // --- Magnetic CTA buttons ---
        const magneticBtns = document.querySelectorAll(
            '.btn-primary, .btn-ghost, .nav-cta .btn, .bento-cta, .map-reset-btn'
        );
        const strength = 0.25;
        magneticBtns.forEach((btn) => {
            btn.classList.add('magnetic');
            let mRaf = null;
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = (e.clientX - rect.left - rect.width / 2);
                const y = (e.clientY - rect.top - rect.height / 2);
                if (mRaf) cancelAnimationFrame(mRaf);
                mRaf = requestAnimationFrame(() => {
                    btn.style.transform = `translate(${(x * strength).toFixed(1)}px, ${(y * strength).toFixed(1)}px)`;
                });
            });
            btn.addEventListener('mouseleave', () => {
                if (mRaf) cancelAnimationFrame(mRaf);
                btn.style.transform = '';
            });
        });
    }
})();
