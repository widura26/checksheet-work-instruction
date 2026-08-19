<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SttpDalmatDatangOrg extends Model
{
    use HasFactory;

    protected $table = 'sttp_dalmat_datang_orgs';

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

    public function sttpDalmatDatang()
    {
        return $this->belongsTo(SttpDalmatDatang::class);
    }

    public function diminta()
    {
        return $this->belongsTo(User::class);
    }

    public function mengetahui()
    {
        return $this->belongsTo(User::class);
    }

    public function diterima()
    {
        return $this->belongsTo(User::class);
    }
}
