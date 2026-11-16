<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_departemen',
        'is_active',
    ];

    public function bagian()
    {
        return $this->hasMany(Bagian::class);
    }
}
