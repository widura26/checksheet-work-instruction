<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LapWi extends Model
{
    use HasFactory;

    protected $table = 'lap_wi';

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
        return $this->belongsTo(MdHeader::class, 'md_header_id');
    }

    public function oka()
    {
        return $this->belongsTo(Oka::class, 'oka_id');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'project_id');
    }
}
