<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proses extends Model
{
    protected $table = 'processes';

    protected $fillable = [
        'id_proses',
        'kode_proses',
        'nama',
        'id_kel',
    ];
}
