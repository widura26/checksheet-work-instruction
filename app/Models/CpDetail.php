<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CpDetail extends Model
{
    use HasFactory;

    protected $table = 'cp_details';

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

    public function exp()
    {
        return $this->belongsTo(Exp::class, 'exp_id');
    }

    public function mdHeader()
    {
        return $this->belongsTo(MdHeader::class, 'md_header_id');
    }

    public function oka()
    {
        return $this->belongsTo(Oka::class, 'oka_id');
    }

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class, 'material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bpms()
    {
        return $this->belongsToMany(Bpm::class, 'bpm_cps');
    }
}
