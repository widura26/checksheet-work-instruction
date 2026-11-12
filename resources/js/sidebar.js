const sidebar = document.getElementById('sidebar');
const toggleButton = document.getElementById('sidebar-toggle');
const backdrop = document.getElementById('sidebar-backdrop');

if (sidebar && toggleButton) {
    const body = document.body;

    const isOpen = () => body.classList.contains('sidebar-open');

    const setOpen = (open) => {
        body.classList.toggle('sidebar-open', open);
        toggleButton.setAttribute('aria-expanded', String(open));
        sidebar.setAttribute('aria-hidden', String(!open));
        sidebar.inert = !open;
    };

    toggleButton.addEventListener('click', () => setOpen(!isOpen()));

    backdrop?.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false);
            toggleButton.focus();
        }
    });

    setOpen(false);
}
