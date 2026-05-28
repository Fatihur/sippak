import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('filePreviewer', () => ({
    files: [],
    objectUrls: [],
    handleFiles(event) {
        this.revokeUrls();
        this.files = Array.from(event.target.files || []).map((file, index) => {
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            const url = (isImage || isPdf) ? URL.createObjectURL(file) : null;
            if (url) this.objectUrls.push(url);

            return {
                id: `${file.name}-${file.size}-${index}`,
                name: file.name,
                size: this.formatSize(file.size),
                isImage,
                url,
                icon: isPdf ? 'fa-file-pdf' : 'fa-file-word',
            };
        });
    },
    remove(index) {
        this.files.splice(index, 1);
        // File inputs cannot safely remove individual File objects without rebuilding DataTransfer.
        // The preview is removed for user clarity; submitting still uses the selected input files.
    },
    formatSize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    },
    revokeUrls() {
        this.objectUrls.forEach((url) => URL.revokeObjectURL(url));
        this.objectUrls = [];
    },
}));

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
