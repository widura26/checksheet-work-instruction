<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.head')
</head>

<body>
    @include('partials.offline-banner')
    @include('partials.sidebar')
    <div class="sidebar-overlay-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>

    <div class="app-shell">
        @include('partials.header')
        <main class="app-content">
            <div class="container-fluid app-container">
                @yield('content')
            </div>
        </main>
        @include('partials.footer')
    </div>
    @include('partials.scripts')
</body>

</html>
