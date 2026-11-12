<?php

// app/Jobs/ImportExcelJob.php
namespace App\Jobs;

use App\Support\ExcelReader;
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

    public function __construct(public string $path) {}

    public function handle(): void
    {
        $file = Storage::path($this->path);
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
                    $buffer = [];
                }
            }
        }

        if ($buffer) {
            DB::table(self::TABLE)->insert($buffer);
            $total += count($buffer);
        }

        Storage::delete($this->path);
        Log::info("Import selesai: {$total} baris");
    }
}

?>
