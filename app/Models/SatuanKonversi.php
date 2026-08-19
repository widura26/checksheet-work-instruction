<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanKonversi extends Model
{
    use HasFactory;
    protected $table = 'satuan_konversi';
    protected $fillable = [
        'kode_material',
        'satuan_sap',
        'satuan_distribusi',
        'berat_per_satuan_distribusi',
        'nilai_konversi',
    ];
    // untuk mengkonversi qty dari satuan distribusi ke satuan sap
    public function convertToBase($qty)
    {
        return $qty * $this->nilai_konversi;
    }
}
