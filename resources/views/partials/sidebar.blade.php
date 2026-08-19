<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header bg-white">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-title">Central Tools</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}">
                    </use>
                </svg>
                Dashboard
            </a>
        </li>
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('daftar-kode-material.index') ? 'active' : '' }}"
                    href="{{ route('daftar-kode-material.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-storage') }}">
                        </use>
                    </svg>
                    Daftar Kode Material
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('barcode.print') ? 'active' : '' }}"
                    href="{{ route('barcode.print') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-barcode') }}">
                        </use>
                    </svg>
                    Daftar Barcode
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('konversi-satuan.index') ? 'active' : '' }}"
                    href="{{ route('konversi-satuan.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-calculator') }}">
                        </use>
                    </svg>
                    Konversi Satuan
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}"
                    href="{{ route('projects.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-folder') }}">
                        </use>
                    </svg>
                    Daftar Proyek
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li class="nav-group {{ request()->routeIs('lokasi.*') ? 'show active' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-location-pin') }}">
                        </use>
                    </svg>
                    Lokasi
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['lokasi.departemen.index']) ? 'active' : '' }}"
                            href="{{ route('lokasi.departemen.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Departemen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['lokasi.bagian.index']) ? 'active' : '' }}"
                            href="{{ route('lokasi.bagian.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Bagian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['lokasi.unit.index']) ? 'active' : '' }}"
                            href="{{ route('lokasi.unit.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Unit
                        </a>
                    </li>
                    @if (Auth::user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs(['posisi-ct.*']) ? 'active' : '' }}"
                                href="{{ route('posisi-ct.index') }}">
                                <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Central Tool
                            </a>
                        </li>
                    @endif
                </ul>
        @endif
        </li>
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('viewer') || Auth::user()->hasRole('staff'))
            <li class="nav-group {{ request()->routeIs(['laporan.distribusi']) ? 'show active' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-chart-line') }}">
                        </use>
                    </svg>
                    Laporan
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['laporan.distribusi']) ? 'active' : '' }}"
                            href="{{ route('laporan.distribusi') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Distribusi Material
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('spm.index') ? 'active' : '' }}"
                    href="{{ route('spm.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-file') }}">
                        </use>
                    </svg>
                    SPM
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('viewer'))
            <li
                class="nav-group {{ request()->routeIs(['transfer-posting.index', 'transfer-posting.create', 'transfer-posting.show', 'transfer-posting.return']) ? 'active' : '' }}">
                <a class="nav-link nav-group-toggle">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-transfer') }}">
                        </use>
                    </svg>
                    Transfer Posting
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['transfer-posting.index', 'transfer-posting.create']) ? 'active' : '' }}"
                            href="{{ route('transfer-posting.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Daftar Transfer Posting
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['transfer-posting.return']) ? 'active' : '' }}"
                            href="{{ route('transfer-posting.return') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Pengembalian
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('staff_ct')) --}}
        <li
            class="nav-group {{ request()->routeIs(['tool.index', 'consumable.index', 'apd.index', 'distribusi.index', 'update-stok-tool.index', 'pengurangan-tool.index', 'tool.create', 'consumable.create', 'apd.create', 'tool.edit', 'consumable.edit', 'apd.edit', 'penggantian-tool.index', 'penggantian-tool.create']) ? 'show active' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-applications') }}">
                    </use>
                </svg>
                Central Tool
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['tool.index', 'tool.create', 'tool.edit']) ? 'active' : '' }}"
                        href="{{ route('tool.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Tool
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('consumable.index') ? 'active' : '' }}"
                        href="{{ route('consumable.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Consumable
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('apd.index') ? 'active' : '' }}"
                        href="{{ route('apd.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>APD
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('distribusi.index') ? 'active' : '' }}"
                        href="{{ route('distribusi.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Distribusi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['penggantian-tool.index', 'penggantian-tool.create']) ? 'active' : '' }}"
                        href="{{ route('penggantian-tool.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Penggantian Tool
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['update-stok-tool.index', 'update-stok-tool.create']) ? 'active' : '' }}"
                        href="{{ route('update-stok-tool.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Tambah/Update Stok
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['dokumen-pengambilan.index']) ? 'active' : '' }}"
                        href="{{ route('dokumen-pengambilan.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Dokumen BPM/MEMO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['pengurangan-tool.index', 'pengurangan-tool.create']) ? 'active' : '' }}"
                        href="{{ route('pengurangan-tool.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Pengurangan Stok
                    </a>
                </li>
            </ul>
        </li>
        {{-- @endif --}}

        {{-- @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff')) --}}
        <li
            class="nav-group {{ request()->routeIs(['tool-rusak.index', 'tindak-lanjut-tool-rusak.index']) ? 'show active' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-settings') }}">
                    </use>
                </svg>
                Tool Rusak
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['tool-rusak.index']) ? 'active' : '' }}"
                        href="{{ route('tool-rusak.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Daftar Tool
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['tindak-lanjut-tool-rusak.index']) ? 'active' : '' }}"
                        href="{{ route('tindak-lanjut-tool-rusak.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Tidak Bisa Diperbaiki
                    </a>
                </li>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('viewer') || Auth::user()->hasRole('staff'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['tool-dibuang.index']) ? 'active' : '' }}"
                            href="{{ route('tool-dibuang.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Tool Dibuang
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('viewer'))
            <li
                class="nav-group {{ request()->routeIs(['pr.index', 'pr.create', 'pr.show', 'pr.edit']) ? 'show active' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-clipboard') }}">
                        </use>
                    </svg>
                    Purchase Requisition
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['pr.index']) ? 'active' : '' }}"
                            href="{{ route('pr.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Daftar PR
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }}"
                    href="{{ route('staff.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-people') }}">
                        </use>
                    </svg>
                    Pengguna
                </a>
            </li>
        @endif
        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('viewer'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('karyawan.index') ? 'active' : '' }}"
                    href="{{ route('karyawan.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-user') }}">
                        </use>
                    </svg>
                    Daftar Karyawan
                </a>
            </li>
        @endif
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
