<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GrafikMd extends Model
{
    use HasFactory;

    protected $table = 'grafik_md';

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

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'project_id');
    }
}
