<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDaftarDistribusiTool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function daftarDistribusiTool()
    {
        return $this->belongsTo(DaftarDistribusiTool::class);
    }

    public function detailKerusakanTools()
    {
        return $this->hasMany(DetailKerusakanTool::class, 'detail_daftar_distribusi_tools_id', 'id');
    }
}
