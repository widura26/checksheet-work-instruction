<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolDibuang extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function daftarToolDibuang()
    {
        return $this->hasMany(DaftarToolDibuang::class);
    }
}
