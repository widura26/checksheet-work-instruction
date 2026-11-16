<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Login - Worksheet</title>
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
    <meta name="theme-color" content="#ffffff" />
    <link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
    <style>
        :root {
            --login-bg: #ffffff;
            --login-card-bg: #ffffff;
            --login-border: #e9ecef;
            --login-text: #212529;
            --login-muted: #6c757d;
            --login-focus: #212529;
        }

        body {
            background: var(--login-bg);
            color: var(--login-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: #ffffff;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--login-card-bg);
            border: 1px solid var(--login-border);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
        }

        .login-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1.75rem;
        }

        .login-logo img {
            height: 72px;
            width: auto;
            max-width: 100%;
        }

        .login-title {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.25rem;
            color: var(--login-text);
        }

        .login-subtitle {
            font-size: 0.875rem;
            text-align: center;
            color: var(--login-muted);
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: var(--login-text);
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            font-size: 0.95rem;
            background: #fff;
            box-shadow: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            border-color: #adb5bd;
            box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.06);
        }

        .btn-login {
            width: 100%;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            background: #111827;
            border: 1px solid #111827;
            color: #fff;
            transition: background 0.15s ease, transform 0.05s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: #000000;
            border-color: #000000;
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .invalid-feedback {
            font-size: 0.8125rem;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="INKA">
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="nip" class="form-label">NIP</label>
                    <input id="nip" class="form-control @error('nip') is-invalid @enderror" type="text"
                        name="nip" placeholder="Masukkan NIP" value="{{ old('nip') }}" required autofocus />
                    @error('nip')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-control @error('password') is-invalid @enderror"
                        type="password" name="password" placeholder="Masukkan password" required />
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button class="btn btn-login" type="submit">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
