import Alpine from 'alpinejs';

const localIsoDate = (date = new Date()) =>
    `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

/**
 * Date availability checker. `booked` maps Y-m-d to "booked" or "limited";
 * any other future date is open.
 */
Alpine.data('dateChecker', (booked, initial = '') => ({
    date: initial,
    today: localIsoDate(),
    get status() {
        if (!this.date) return null;
        if (this.date <= this.today) return 'past';
        return booked[this.date] ?? 'open';
    }, s
    get label() {
        if (!this.date) return '';
        const [year, month, day] = this.date.split('-').map(Number);
        return new Date(year, month - 1, day).toLocaleDateString('en-GB', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    },
}));

/**
 * Booking request form. `tours` maps a tour slug to its title, pickup time and rates,
 * so the price shown always matches the tour and rate selected.
 */
Alpine.data('bookingForm', (tours, initial = {}) => ({
    tour: initial.tour ?? '',
    rate: initial.rate ?? '',
    adults: Number(initial.adults ?? 2),
    children: Number(initial.children ?? 0),
    init() {
        this.syncRate();
        this.$watch('tour', () => this.syncRate());
    },
    get current() {
        return tours[this.tour] ?? null;
    },
    get rates() {
        return this.current?.rates ?? [];
    },
    get selectedRate() {
        return this.rates.find((rate) => rate.key === this.rate) ?? null;
    },
    get guests() {
        return Math.max(0, Number(this.adults) || 0) + Math.max(0, Number(this.children) || 0);
    },
    // Keep the chosen rate valid when the tour changes: a tour with one price has one rate.
    syncRate() {
        if (!this.rates.some((rate) => rate.key === this.rate)) {
            this.rate = this.rates[0]?.key ?? '';
        }
    },
}));

/** Multi-step form (booking request). Validates the visible step with native constraints before moving on. */
Alpine.data('stepper', (steps, start = 0) => ({
    step: start,
    steps,
    next() {
        const fields = this.$root.querySelectorAll(`[data-step="${this.step}"] :is(input, select, textarea)`);
        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return;
            }
        }
        this.go(this.step + 1);
    },
    go(index) {
        this.step = Math.max(0, Math.min(steps - 1, index));
        this.$nextTick(() => this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' }));
    },
}));

/** Photo gallery with a keyboard-friendly lightbox. */
Alpine.data('gallery', (images) => ({
    images,
    open: false,
    index: 0,
    show(index) {
        this.index = index;
        this.open = true;
    },
    move(step) {
        this.index = (this.index + step + this.images.length) % this.images.length;
    },
}));

/** Count-up for stat numbers when scrolled into view. */
Alpine.data('countUp', (target, duration = 1400) => ({
    // Starts at the real number so it is never wrong if the animation cannot run.
    value: target,
    init() {
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }
        const observer = new IntersectionObserver(([entry]) => {
            if (!entry.isIntersecting) return;
            observer.disconnect();
            const start = performance.now();
            const tick = (now) => {
                const progress = Math.min(1, (now - start) / duration);
                this.value = Math.round(target * (1 - Math.pow(1 - progress, 3)));
                if (progress < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            // Animation frames pause in background tabs; make sure the final number always lands.
            setTimeout(() => (this.value = target), duration + 400);
        });
        observer.observe(this.$el);
    },
}));

/** File input with a friendly filename readout. */
Alpine.data('fileField', () => ({
    name: '',
    pick(event) {
        const files = [...event.target.files];
        this.name = files.length > 1 ? `${files.length} files selected` : (files[0]?.name ?? '');
    },
}));

window.Alpine = Alpine;
Alpine.start();

// Scroll reveal
document.documentElement.classList.add('js');
const reveal = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                reveal.unobserve(entry.target);
            }
        }
    },
    { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
);
document.querySelectorAll('[data-reveal]').forEach((el) => reveal.observe(el));
