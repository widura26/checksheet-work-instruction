<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WiFinish extends Model
{
    use HasFactory;

    protected $table = 'wi_finishes';

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

    public function wiTrn()
    {
        return $this->belongsTo(WiTrn::class);
    }

    public function jenisKereta()
    {
        return $this->belongsTo(JenisKereta::class);
    }

    public function sttpp()
    {
        return $this->belongsTo(Sttpp::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}