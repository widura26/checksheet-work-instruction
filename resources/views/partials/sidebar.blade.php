<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header bg-white">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-title">Checksheet Work Instruction</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}">
                    </use>
                </svg>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('project-code.index') ? 'active' : '' }}"
                href="{{ route('project-code.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-folder') }}">
                    </use>
                </svg>
                Kode Proyek
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('processes.index') ? 'active' : '' }}"
                href="{{ route('processes.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-cog') }}">
                    </use>
                </svg>
                Proses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('md.index') ? 'active' : '' }}"
                href="{{ route('md.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-pencil') }}">
                    </use>
                </svg>
                Manufacture Drawing
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('cp.index') ? 'active' : '' }}"
                href="{{ route('cp.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-calculator') }}">
                    </use>
                </svg>
                Cutting Plan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('grafik.index') ? 'active' : '' }}"
                href="{{ route('grafik.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-chart-line') }}">
                    </use>
                </svg>
                Grafik
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('wi.index') ? 'active' : '' }}"
                href="{{ route('wi.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-document') }}">
                    </use>
                </svg>
                Work Instruction
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('kode-material.index') ? 'active' : '' }}"
                href="{{ route('kode-material.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-storage') }}">
                    </use>
                </svg>
                Data Material
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barcode.index') ? 'active' : '' }}"
                href="{{ route('barcode.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-barcode') }}">
                    </use>
                </svg>
                Barcode
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('sttp.index') ? 'active' : '' }}"
                href="{{ route('sttp.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-file') }}">
                    </use>
                </svg>
                STTP
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ekspedisi.index') ? 'active' : '' }}"
                href="{{ route('ekspedisi.index') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-truck') }}">
                    </use>
                </svg>
                Ekspedisi
            </a>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-print') }}">
                    </use>
                </svg>
                Laporan
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan-work-instruction.index') ? 'active' : '' }}"
                        href="{{ route('laporan-work-instruction.index') }}">
                        Work Instruction
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan-surat-permintaan-material.index') ? 'active' : '' }}"
                        href="{{ route('laporan-surat-permintaan-material.index') }}">
                        Surat Permintaan Material
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan-cutting-plan.index') ? 'active' : '' }}"
                        href="{{ route('laporan-cutting-plan.index') }}">
                        Cutting Plan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan-bon-permintaan-material.index') ? 'active' : '' }}"
                        href="{{ route('laporan-bon-permintaan-material.index') }}">
                        Bon Permintaan Material
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start bg-transparent border-0">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}">
                        </use>
                    </svg>
                    Log out
                </button>
            </form>
        </li>
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
