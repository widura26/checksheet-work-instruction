<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Login - Worksheet</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('template/assets/favicon/apple-icon-57x57.png') }}" />
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('template/assets/favicon/apple-icon-60x60.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('template/assets/favicon/apple-icon-72x72.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('template/assets/favicon/apple-icon-76x76.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('template/assets/favicon/apple-icon-114x114.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('template/assets/favicon/apple-icon-120x120.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('template/assets/favicon/apple-icon-144x144.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('template/assets/favicon/apple-icon-152x152.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('template/assets/favicon/apple-icon-180x180.png') }}" />
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
    <link rel="manifest" href="{{ asset('template/assets/favicon/manifest.json') }}" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="{{ asset('template/assets/favicon/ms-icon-144x144.png') }}" />
    <meta name="theme-color" content="#ffffff" />
    <!-- Vendors styles-->
    <link rel="stylesheet" href="{{ asset('template/vendors/simplebar/css/simplebar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/css/vendors/simplebar.css') }}" />
    <!-- Main styles for this application-->
    <link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="{{ asset('template/css/examples.css') }}" rel="stylesheet" />
    <script src="{{ asset('template/js/config.js') }}"></script>
    <script src="{{ asset('template/js/color-modes.js') }}"></script>
</head>

<body>
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-7 p-4 mb-0">
                            <div class="card-body">
                                <h4 class="mb-3">Worksheet</h4>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use
                                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-user') }}">
                                                </use>
                                            </svg></span>
                                        <input class="form-control @error('nip') is-invalid @enderror" type="text"
                                            name="nip" placeholder="NIP" value="{{ old('nip') }}" required
                                            autofocus />
                                        @error('nip')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="input-group mb-4">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use
                                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-lock-locked') }}">
                                                </use>
                                            </svg></span>
                                        <input class="form-control @error('password') is-invalid @enderror"
                                            type="password" name="password" placeholder="Password" required />
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-primary px-4" type="submit">
                                                Login
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card col-md-5 text-white bg-white py-5">
                            <div class="card-body text-center">
                                <div>
                                    <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}"
                                        alt="Dashboard" style="width: 80%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('template/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('template/vendors/simplebar/js/simplebar.min.js') }}"></script>
    <script type="module"></script>
</body>

</html>
