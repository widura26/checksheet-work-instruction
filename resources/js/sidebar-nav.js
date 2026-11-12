const sidebarNav = document.querySelector('#sidebar .sidebar-nav');

if (sidebarNav) {
    const getParts = (group) => ({
        toggle: group.querySelector(':scope > .nav-group-toggle'),
        items: group.querySelector(':scope > .nav-group-items'),
    });

    const setExpanded = (group, expanded) => {
        const { toggle, items } = getParts(group);

        group.classList.toggle('show', expanded);
        toggle?.setAttribute('aria-expanded', String(expanded));

        if (items) {
            items.inert = !expanded;
        }
    };

    sidebarNav.addEventListener('click', (event) => {
        const toggle = event.target.closest?.('.nav-group-toggle');

        if (!toggle || !sidebarNav.contains(toggle)) {
            return;
        }

        event.preventDefault();

        const group = toggle.closest('.nav-group');

        if (!group) {
            return;
        }

        const isExpanded = group.classList.contains('show');

        // Hanya satu grup yang terbuka pada satu waktu.
        sidebarNav.querySelectorAll('.nav-group.show').forEach((openGroup) => {
            if (openGroup !== group) {
                setExpanded(openGroup, false);
            }
        });

        setExpanded(group, !isExpanded);
    });
}
