<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ImportTracker
{
    private const TTL_HOURS = 24;
    private const VERSION_KEY = 'mp:version';

    public static function start(string $importId): void
    {
        self::put($importId, [
            'status' => 'pending',
            'imported' => 0,
            'message' => 'Import menunggu antrean.',
        ]);
    }

    public static function processing(string $importId): void
    {
        self::put($importId, [
            'status' => 'processing',
            'message' => 'Import sedang diproses.',
        ]);
    }

    public static function addImported(string $importId, int $count): void
    {
        $state = self::get($importId) ?? [];
        $imported = (int) ($state['imported'] ?? 0) + max(0, $count);

        self::put($importId, [
            'status' => 'processing',
            'imported' => $imported,
            'message' => 'Import sedang diproses.',
        ]);
    }

    public static function completed(string $importId, int $imported): void
    {
        self::put($importId, [
            'status' => 'completed',
            'imported' => max(0, $imported),
            'message' => 'Import telah selesai.',
        ]);

        self::invalidateCache();
    }

    public static function failed(string $importId, string $error): void
    {
        self::put($importId, [
            'status' => 'failed',
            'error' => $error,
            'message' => 'Import gagal diproses.',
        ]);
    }

    public static function get(string $importId): ?array
    {
        $state = Cache::get(self::key($importId));

        return is_array($state) ? $state : null;
    }

    public static function invalidateCache(): void
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        Cache::forever(self::VERSION_KEY, $version + 1);
    }

    private static function put(string $importId, array $data): void
    {
        $state = array_merge(
            ['import_id' => $importId],
            self::get($importId) ?? [],
            $data,
            ['updated_at' => now()->toIso8601String()]
        );

        Cache::put(self::key($importId), $state, now()->addHours(self::TTL_HOURS));
    }

    private static function key(string $importId): string
    {
        return 'mp:import:' . $importId;
    }
}
