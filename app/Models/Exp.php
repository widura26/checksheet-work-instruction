<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exp extends Model
{
    use HasFactory;

    protected $table = 'exps';

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
        return $this->belongsTo(Oka::class, 'oka_id');
    }

    public function cpDetail()
    {
        return $this->belongsTo(CpDetail::class, 'cp_detail_id');
    }

    public function mdHeader()
    {
        return $this->belongsTo(MdHeader::class, 'md_header_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function cpDetails()
    {
        return $this->belongsToMany(CpDetail::class, 'exp_cps');
    }
}
