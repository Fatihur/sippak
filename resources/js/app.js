import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('theme', {
    theme: 'light',
    init() {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        this.theme = savedTheme || systemTheme;
        this.updateTheme();
    },
    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.theme);
        this.updateTheme();
    },
    updateTheme() {
        document.documentElement.classList.toggle('dark', this.theme === 'dark');
    },
});

Alpine.store('sidebar', {
    isExpanded: window.innerWidth >= 1280,
    isMobileOpen: false,
    isHovered: false,
    toggleExpanded() {
        this.isExpanded = !this.isExpanded;
        this.isMobileOpen = false;
        localStorage.setItem('sippak-sidebar-expanded', this.isExpanded ? 'true' : 'false');
    },
    toggleMobileOpen() {
        this.isMobileOpen = !this.isMobileOpen;
    },
    setMobileOpen(value) {
        this.isMobileOpen = value;
    },
    setHovered(value) {
        if (window.innerWidth >= 1280 && !this.isExpanded) {
            this.isHovered = value;
        }
    },
});

Alpine.start();
