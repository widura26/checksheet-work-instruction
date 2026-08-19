<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SpmOrg extends Model
{
    use HasFactory;

    protected $table = 'spm_orgs';

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
