<base href="./" />
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
<title>@yield('title', 'Central Tools')</title>
<link rel="icon" sizes="57x57" href="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
<link rel="manifest" href="{{ asset('template/assets/favicon/manifest.json') }}" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="{{ asset('template/assets/favicon/ms-icon-144x144.png') }}" />
<meta name="theme-color" content="#ffffff" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ asset('template/vendors/simplebar/css/simplebar.css') }}" />
<link rel="stylesheet" href="{{ asset('template/css/vendors/simplebar.css') }}" />
<link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style></style>
@stack('css')
