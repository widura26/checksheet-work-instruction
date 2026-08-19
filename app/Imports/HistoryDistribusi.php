<?php

namespace App\Imports;

use App\Models\DistribusiTool;
use App\Models\Karyawan;
use App\Models\Tools;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HistoryDistribusi implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $projectId = '';
            if (trim($row['project']) === 'PPCW') {
                $projectId = 'B-24001.1';
            } else {
                $projectId = 'S-22002.1';
            }
            $karyawan = Karyawan::where('nip', trim((string)$row['nip']))->first() ?? null;
            if ($karyawan == null) {
                continue;
            }
            $material = Tools::where('kode_material', trim($row['kode_material']))->first() ?? null;
            if ($material == null) {
                continue;
            }
            $distribusiTool = DistribusiTool::create([
                'nama_peminjam' => $karyawan->nama,
                'nip' => $karyawan->nip,
                'unit_id' => $karyawan->unit_id,
                'bagian_id' => $karyawan->bagian_id,
                'departemen_id' => $karyawan->departemen_id,
                'project_id' => $projectId,
                'type' => 'penyerahan',
                'kategori' => trim($row['kategori']),
                'status' => 'diserahkan',
                'tanggal_pinjam_diserahkan' => Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d'),
                'user_id' => Auth::user()->id,
                'public_id' => Str::uuid(),
            ]);
            $distribusiTool->daftarDistribusiTool()->create([
                'tool_id' => $material->id,
                'posisi_ct_id' => $material->posisi_ct_id,
                'jumlah_distribusi' => (float)trim($row['qty']),
                'jumlah_lapangan' => (float)trim($row['qty']),
            ]);
        }
    }
}
