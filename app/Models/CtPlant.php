<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CtPlant extends Model
{
    use HasFactory;

    protected $table = 'ct_plants';

    protected $guarded = ['id'];

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

    public function oka()
    {
        return $this->belongsTo(Oka::class);
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function cpDetail()
    {
        return $this->belongsTo(CpDetail::class);
    }

    public function bpm()
    {
        return $this->belongsTo(Bpm::class);
    }

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class);
    }

    public function jenisKereta()
    {
        return $this->belongsTo(JenisKereta::class);
    }
}