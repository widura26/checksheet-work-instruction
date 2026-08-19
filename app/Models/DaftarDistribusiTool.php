<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarDistribusiTool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function getJumlahDistribusiFormattedAttribute()
    {
        if ($this->jumlah_distribusi === null) {
            return null;
        }

        return rtrim(rtrim(number_format($this->jumlah_distribusi, 4, '.', ''), '0'), '.');
    }
    public function distribusiTool()
    {
        return $this->belongsTo(DistribusiTool::class, 'distribusi_tools_id', 'id');
    }
    public function posisiCT()
    {
        return $this->belongsTo(Posisi_CT::class, 'posisi_ct_id', 'id');
    }
    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(DetailDaftarDistribusiTool::class);
    }
}
