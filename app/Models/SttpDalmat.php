<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SttpDalmat extends Model
{
    use HasFactory;

    protected $table = 'sttp_dalmats';

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

    public function mdHeader()
    {
        return $this->belongsTo(MdHeader::class);
    }

    public function oka()
    {
        return $this->belongsTo(Oka::class);
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}
