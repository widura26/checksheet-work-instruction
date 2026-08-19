<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKerusakanTool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['tanggal_spr' => 'date'];

    public function detailDaftarDistribusiTool()
    {
        return $this->belongsTo(DetailDaftarDistribusiTool::class, 'detail_daftar_distribusi_tools_id', 'id');
    }

    public function penguranganTool()
    {
        return $this->hasOne(PenguranganTool::class, 'detail_kerusakan_tools_id', 'id');
    }

    public function prDetail()
    {
        return $this->hasOne(PurchaseRequisitionDetail::class, 'detail_kerusakan_tools_id', 'id');
    }
}
