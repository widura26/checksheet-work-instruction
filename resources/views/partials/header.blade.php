@auth
    <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
            <button class="header-toggler" type="button"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                style="margin-inline-start: -14px">
                <svg class="icon icon-lg">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button>
            <ul class="header-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-md">
                            <svg xmlns="http://w3.org" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="5" r="2" />
                                <circle cx="12" cy="12" r="2" />
                                <circle cx="12" cy="19" r="2" />
                            </svg>
                            {{-- <img class="avatar-img"
                                src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" /> --}}
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end pt-0">

                        {{-- USER INFO --}}
                        <div class="dropdown-header bg-body-tertiary text-body-secondary py-3 text-center">
                            <div class="fw-semibold">
                                {{ Auth::user()->name }}
                            </div>
                            <small class="text-muted">
                                {{ Auth::user()->getRoleNames()->first() }}
                            </small>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <svg class="icon me-2">
                                    <use
                                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}">
                                    </use>
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
