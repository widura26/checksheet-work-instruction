<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">

        <li class="nav-title">Main Menu</li>

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'dashboard',
            'icon' => 'cil-speedometer',
            'label' => 'Dashboard'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'grafik.index',
            'icon' => 'cil-chart-line',
            'label' => 'Grafik'
        ])

        <li class="nav-title">Master Data</li>

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

        <li class="nav-title">Transaksi</li>

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'check-sheet.index',
            'icon' => 'cil-task',
            'label' => 'Check Sheet'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'md.index',
            'icon' => 'cil-pencil',
            'label' => 'Manufacture Drawing'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'cutting-plan.index',
            'icon' => 'cil-calculator',
            'label' => 'Cutting Plan'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'wi.index',
            'icon' => 'cil-description',
            'label' => 'Work Instruction'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'barcode.index',
            'icon' => 'cil-barcode',
            'label' => 'Barcode'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'sttp.index',
            'icon' => 'cil-file',
            'label' => 'STTPP'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'ekspedisi.index',
            'icon' => 'cil-truck',
            'label' => 'Ekspedisi'
        ])

        <li class="nav-title">Laporan</li>

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-group',
            'icon' => 'cil-print',
            'label' => 'Laporan',
            'subitems' => [
                [
                    'navtype' => 'nav-item',
                    'route' => 'laporan-work-instruction.index',
                    'label' => 'Work Instruction',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'laporan-surat-permintaan-material.index',
                    'label' => 'Surat Permintaan Material',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'laporan-cutting-plan.index',
                    'label' => 'Cutting Plan',
                ],
                [
                    'navtype' => 'nav-item',
                    'route' => 'laporan-bon-permintaan-material.index',
                    'label' => 'Bon Permintaan Material',
                ],
            ]
        ])

        <li class="nav-title">User Management</li>

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
            ]
        ])

        <li class="nav-title">Akun</li>

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'logout',
            'icon' => 'cil-account-logout',
            'label' => 'Log out'
        ])

    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
