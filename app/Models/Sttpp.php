<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sttpp extends Model
{
    use HasFactory;

    protected $table = 'sttpps';

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

    public function pengirim()
    {
        return $this->belongsTo(User::class);
    }

    public function penerima()
    {
        return $this->belongsTo(User::class);
    }
}
