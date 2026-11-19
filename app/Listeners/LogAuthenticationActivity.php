<?php

namespace App\Listeners;

use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    public function handle($event): void
    {
        if ($event instanceof Login) {
            ActivityLogger::record(
                'login',
                'Login berhasil'.($event->user?->email ? " ({$event->user->email})" : ''),
                ['guard' => $event->guard, 'remember' => (bool) $event->remember],
                $event->user,
                'Auth',
                $event->user
            );

            return;
        }

        if ($event instanceof Logout) {
            ActivityLogger::record(
                'logout',
                'Logout'.($event->user?->email ? " ({$event->user->email})" : ''),
                ['guard' => $event->guard],
                $event->user,
                'Auth',
                $event->user
            );

            return;
        }

        if ($event instanceof Failed) {
            // Jangan pernah menyimpan credentials mentah (berisi password).
            $email = data_get($event->credentials, 'email')
                ?? data_get($event->credentials, 'nip');

            ActivityLogger::record(
                'login_failed',
                'Login gagal'.($email ? " ({$email})" : ''),
                ['guard' => $event->guard, 'email' => $email],
                null,
                'Auth'
            );
        }
    }
}
