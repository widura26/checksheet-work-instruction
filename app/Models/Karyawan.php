<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'daftar_karyawans';

    protected $primaryKey = 'nip';

    protected $keyType = 'string';

    protected $fillable = ['nip', 'nama', 'departemen_id', 'bagian_id', 'unit_id', 'jabatan'];

    public function getRouteKeyName()
    {
        return 'nip';
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
