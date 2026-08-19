<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bagian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama_bagian', 'lokasi', 'departemen_id', 'is_active'];

    public function materials()
    {
        return $this->hasMany(Material::class, 'lokasi', 'lokasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function unit()
    {
        return $this->hasMany(Unit::class);
    }

    public function distribusiTools()
    {
        return $this->hasMany(DistribusiTool::class, 'bagian_id');
    }
}
