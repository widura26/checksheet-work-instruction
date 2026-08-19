<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarKodeMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_material',
        'satuan',
        'deskripsi',
        'spesifikasi',
    ];
}
