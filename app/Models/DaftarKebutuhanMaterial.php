<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DaftarKebutuhanMaterial extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'sum'       => 'float',
        'kebutuhan' => 'float',
        'qty_pr_ve' => 'float',
        'qty_pr_ms' => 'float',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
