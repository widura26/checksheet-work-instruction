@auth
    <header class="header header-sticky p-0 mb-3 mb-md-4 app-header">
        <div class="container-fluid border-bottom app-header-inner">
            <div class="app-header-left">
                <button class="header-toggler app-sidebar-toggle" type="button" id="sidebar-toggle"
                    aria-controls="sidebar" aria-expanded="false" aria-label="Buka/tutup sidebar">
                    <svg class="icon icon-lg" aria-hidden="true">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                    </svg>
                </button>
                <a class="app-header-brand" href="{{ url('/dashboard') }}" aria-label="Dashboard">
                    <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="INKA">
                </a>
            </div>
            <ul class="header-nav ms-auto app-header-right">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 py-2" data-coreui-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-none d-md-inline text-truncate app-header-user">{{ Auth::user()->name }}</span>
                        <span class="app-header-avatar" aria-hidden="true">
                            <svg class="icon">
                                <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-user') }}"></use>
                            </svg>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end pt-0">
                        <div class="dropdown-header bg-body-tertiary text-body-secondary py-3 text-center">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ Auth::user()->getRoleNames()->first() }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <svg class="icon me-2">
                                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}"></use>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </header>
@endauth
