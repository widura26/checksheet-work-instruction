<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
    @livewireStyles
</head>

<body>
    @include('partials.offline-banner')
    @include('partials.sidebar')

    <div class="wrapper d-flex flex-column min-vh-100">
        @include('partials.header')
        <div class="body grow">
            <div class="container-lg px-4">
                @yield('content')
            </div>
        </div>
        @include('partials.footer')
    </div>
    @include('partials.scripts')
    @livewireScripts
</body>

</html>
