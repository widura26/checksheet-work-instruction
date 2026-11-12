<?php

namespace App\Jobs;

use App\Imports\BomDatasetImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportBomDatasetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(
        public string $importId,
        public string $filePath
    ) {
    }

    public function handle(): void
    {
        Excel::import(new BomDatasetImport($this->importId), $this->filePath);

        if (File::exists($this->filePath)) {
            File::delete($this->filePath);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("BOM import {$this->importId} failed: {$e->getMessage()} — file: {$this->filePath}");
    }
}
