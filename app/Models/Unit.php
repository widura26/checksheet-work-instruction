<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_unit',
        'bagian_id',
        'is_active'
    ];

    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    public function distribusiTools()
    {
        return $this->hasMany(DistribusiTool::class, 'unit_id');
    }
}
