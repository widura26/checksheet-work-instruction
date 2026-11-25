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

    /**
     * Nama header Excel yang sudah dinormalisasi => kolom database.
     * Urutan header di file tidak lagi menentukan pemetaan.
     */
    private const HEADER_MAP = [
        'wbs' => 'wbs',
        'sheet asal' => 'sheet_asal',
        'no' => 'no',
        'rev' => 'rev',
        'kode material' => 'kode_material',
        'kode material konversi' => 'kode_material_konversi',
        'kode material delete' => 'kode_material_delete',
        'deskripsi material' => 'deskripsi_material',
        'group' => 'group',
        'lot' => 'lot',
        'trainset' => 'lot', // alias header lama
        'kebutuhan' => 'kebutuhan',
        'tindak lanjut' => 'tindak_lanjut',
        'no pr' => 'no_pr',
        'qty pr' => 'qty_pr',
        'satuan' => 'satuan',
        'deliv date sap' => 'deliv_date_sap',
        'status' => 'status',
        'no po' => 'no_po',
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
                $columns = [];

                foreach (ExcelReader::rows($file, $sheetFile) as $rowNumber => $cells) {
                    if ($rowNumber === 1) {
                        $columns = self::columnsFromHeader($cells);

                        if ($columns === []) {
                            Log::warning("Sheet '{$sheetName}' dilewati: tidak ada header yang dikenali.", [
                                'import_id' => $this->importId,
                            ]);
                            break;
                        }

                        continue;
                    }

                    if ($columns === []) {
                        break;
                    }

                    $row = ['id' => (string) Str::uuid()] + array_fill_keys(
                        array_values(self::HEADER_MAP),
                        ''
                    );
                    $filled = false;

                    foreach ($columns as $col => $field) {
                        $value = trim($cells[$col] ?? '');
                        if ($value !== '') {
                            $filled = true;
                        }
                        $row[$field] = $value;
                    }

                    // Fallback ke nama sheet bila kolom "Sheet Asal" kosong.
                    if ($row['sheet_asal'] === '') {
                        $row['sheet_asal'] = $sheetName;
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

    /**
     * @param  array<string, string>  $cells  Baris header: A => "WBS", ...
     * @return array<string, string>          Kolom sheet => field database
     */
    private static function columnsFromHeader(array $cells): array
    {
        $columns = [];

        foreach ($cells as $col => $header) {
            $field = self::HEADER_MAP[self::normalizeHeader((string) $header)] ?? null;

            if ($field !== null) {
                $columns[$col] = $field;
            }
        }

        return $columns;
    }

    private static function normalizeHeader(string $header): string
    {
        return Str::slug(trim($header), ' ');
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
