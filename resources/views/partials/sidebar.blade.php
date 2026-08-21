<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'md.index',
            'icon' => 'cil-pencil',
            'label' => 'Manufacture Drawing'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'cp.index',
            'icon' => 'cil-calculator',
            'label' => 'Cutting Plan'
        ])

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

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'wi.index',
            'icon' => 'cil-document',
            'label' => 'Work Instruction'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'dashboard',
            'icon' => 'cil-speedometer',
            'label' => 'Dashboard'
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
            'route' => 'grafik.index',
            'icon' => 'cil-chart-line',
            'label' => 'Grafik'
        ])

        @include('components.parts.sidebar-list-item', [
            'navtype' => 'nav-item',
            'route' => 'kode-material.index',
            'icon' => 'cil-storage',
            'label' => 'Data Material'
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
