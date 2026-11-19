<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivityLogger
{
    /**
     * Attribute yang tidak boleh disimpan mentah ke activity log.
     */
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'remember_token',
        'token',
        'access_token',
        'refresh_token',
        'secret',
        'api_key',
        'authorization',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Catat aktivitas pengguna. Kegagalan logging tidak boleh menggagalkan aksi utama.
     */
    public static function record(
        string $event,
        string $description,
        array $properties = [],
        ?Model $subject = null,
        string $logName = 'app',
        ?Model $causer = null
    ): void {
        try {
            $causer = $causer ?? Auth::user();

            $payload = array_merge([
                'ip' => request()->ip(),
                'url' => request()->fullUrl(),
                'user_agent' => Str::limit((string) request()->userAgent(), 255),
            ], self::redact($properties));

            if ($causer) {
                $payload['causer_name'] = $causer->name ?? null;
            }

            $activity = activity()
                ->useLog($logName)
                ->event($event)
                ->withProperties($payload)
                ->tap(function ($activity) use ($logName) {
                    $activity->feature = $logName;
                });

            if ($causer) {
                $activity->causedBy($causer);
            }

            if ($subject) {
                $activity->performedOn($subject);
            }

            $activity->log($description);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat activity log: '.$e->getMessage(), [
                'event' => $event,
                'log_name' => $logName,
            ]);
        }
    }

    /**
     * Samarkan nilai sensitif secara rekursif sebelum disimpan.
     */
    public static function redact(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::redact($value);

                continue;
            }

            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = '***';
            }
        }

        return $data;
    }
}
