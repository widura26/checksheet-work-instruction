<?php

// app/Jobs/ImportExcelJob.php
namespace App\Jobs;

use App\Support\ExcelReader;
use App\Support\ImportTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    public $tries = 1;

    private const CHUNK = 500;
    private const TABLE = 'daftar_kebutuhan_materials';

    private const MAP = [
        'A' => 'wbs',
        'B' => 'sheet_asal',
        'C' => 'no',
        'D' => 'rev',
        'E' => 'kode_material',
        'F' => 'kode_material_delete',
        'H' => 'deskripsi_material',
        'I' => 'satuan',
        'J' => 'group',
        'K' => 'lot',
        'L' => 'kebutuhan',
        'M' => 'sum',
        'N' => 'sudah_pr',
        'O' => 'belum_pr',
        'P' => 'tindak_lanjut',
    ];

    public function __construct(public string $importId, public string $path) {}

    public function handle(): void
    {
        ImportTracker::processing($this->importId);

        try {
            $file = Storage::disk('local')->path($this->path);
            $buffer = [];
            $total = 0;

            foreach (ExcelReader::sheets($file) as $sheetName => $sheetFile) {
                foreach (ExcelReader::rows($file, $sheetFile) as $rowNumber => $cells) {
                    if ($rowNumber === 1) {
                        continue;
                    }

                    $row = ['id' => (string) Str::uuid(), 'sheet_asal' => $sheetName];
                    $filled = false;

                    foreach (self::MAP as $col => $field) {
                        $value = trim($cells[$col] ?? '');
                        if ($value !== '') {
                            $filled = true;
                        }
                        $row[$field] = $value;
                    }

                    if (! $filled) {
                        continue;
                    }

                    $buffer[] = $row;

                    if (count($buffer) >= self::CHUNK) {
                        DB::table(self::TABLE)->insert($buffer);
                        $total += count($buffer);
                        ImportTracker::addImported($this->importId, count($buffer));
                        $buffer = [];
                    }
                }
            }

            if ($buffer) {
                DB::table(self::TABLE)->insert($buffer);
                $total += count($buffer);
                ImportTracker::addImported($this->importId, count($buffer));
            }

            ImportTracker::completed($this->importId, $total);
            Log::info("Import selesai: {$total} baris", ['import_id' => $this->importId]);
        } catch (\Throwable $e) {
            Log::error('Import daftar kebutuhan material gagal: ' . $e->getMessage(), [
                'import_id' => $this->importId,
                'exception' => $e,
            ]);

            ImportTracker::failed($this->importId, $e->getMessage());

            throw $e;
        } finally {
            Storage::disk('local')->delete($this->path);
        }
    }

    public function failed(\Throwable $e): void
    {
        ImportTracker::failed($this->importId, $e->getMessage());

        Log::error('Import daftar kebutuhan material gagal: ' . $e->getMessage(), [
            'import_id' => $this->importId,
            'exception' => $e,
        ]);
    }
}
