<div class="sidebar sidebar-dark" id="sidebar" aria-hidden="true" inert>
    <div class="sidebar-header" style="border-bottom: 1px solid #ccc;">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}" aria-label="INKA - Dashboard">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="INKA">
        </a>
    </div>
    <ul class="sidebar-nav" data-simplebar="">
        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-group',
            'icon' => 'cil-print',
            'label' => 'Material Planning',
            'subitems' => [
                [
                    'navtype' => 'nav-item',
                    'route' => 'sap-dataset.index',
                    'label' => 'Dashboard',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'bill-of-material.index',
                    'label' => 'Bill Of Material',
                ],
            ]
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'project-code.index',
            'icon' => 'cil-folder',
            'label' => 'Kode Proyek'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-group',
            'icon' => 'cil-people',
            'label' => 'User Management',
            'subitems' => [
                [
                    'navtype' => 'nav-item',
                    'route' => 'staff.index',
                    'label' => 'Users',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'roles.index',
                    'label' => 'Roles',
                ]
            ]
        ])
    </ul>
</div>
