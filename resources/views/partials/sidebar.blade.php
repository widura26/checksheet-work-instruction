<div class="sidebar sidebar-dark sidebar-overlaid border-end" id="sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
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
            'navtype' => 'nav-item',
            'route' => 'processes.index',
            'icon' => 'cil-cog',
            'label' => 'Kode Proses'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'kode-material.index',
            'icon' => 'cil-storage',
            'label' => 'Kode Material'
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
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'karyawan.index',
                    'label' => 'Daftar Karyawan',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'lokasi.departemen.index',
                    'label' => 'Departemen',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'lokasi.bagian.index',
                    'label' => 'Bagian',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'lokasi.unit.index',
                    'label' => 'Unit',
                ],
            ]
        ])
    </ul>
</div>
