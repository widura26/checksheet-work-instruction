<?php

namespace App\Http\Service;

use App\Models\Spm;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CreateSpmFileService
{
    public function createSpmFile(Spm $spm)
    {
        $spm->load(['items.itemHistory', 'project', 'posisiCt']);
        $posisiCt = $spm->posisiCt->nama_posisi_ct;
        $pos = strpos($posisiCt, '_');
        if ($pos !== false) {
            $posisiCt = strtoupper(substr($posisiCt, $pos + 1));
        } else {
            $posisiCt = null;
        }
        $kodeBagian = (array) DB::table('kode_bagian_spm')->first();
        $spm->load(['items.itemHistory', 'project' => function ($q) {
            $q->withoutGlobalScope('onlyWithStock');
        }, 'posisiCt']);
        $html = view('pages.spm.export-pdf', [
            'spm' => $spm->toArray(),
            'ct'  => $posisiCt,
            'kodeBagian' => $kodeBagian
        ])->render();
        $pdf = Pdf::loadHTML($html)
            ->setPaper([0, 0, 792, 684]);
        $fileName = str_replace('/', '-', $spm->no_spm) . '.pdf';
        $directory = storage_path('app/public/spm_files');
        if (!file_exists($directory)) {
            Storage::disk('public')->makeDirectory('spm_files');
        }
        $path = $directory . '/' . $fileName;
        $pdf->save($path);
        return 'spm_files/' . $fileName;
    }
}
