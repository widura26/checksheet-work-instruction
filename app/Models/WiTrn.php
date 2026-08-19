<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WiTrn extends Model
{
    use HasFactory;

    protected $table = 'wi_trns';

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

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class);
    }

    public function mdHeader()
    {
        return $this->belongsTo(MdHeader::class);
    }

    public function jenisKereta()
    {
        return $this->belongsTo(JenisKereta::class);
    }

    public function bpmCp()
    {
        return $this->belongsTo(BpmCp::class);
    }

    public function cp()
    {
        return $this->belongsTo(Cp::class);
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }

    public function spmIm()
    {
        return $this->belongsTo(SpmIm::class, 'spm_ims_id');
    }

    public function bpm()
    {
        return $this->belongsTo(Bpm::class);
    }

    public function barcode()
    {
        return $this->belongsTo(Barcode::class);
    }

    public function exp()
    {
        return $this->belongsTo(Exp::class);
    }

    public function userCp()
    {
        return $this->belongsTo(User::class);
    }

    public function userPrint()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(WiTrn::class, 'parent_id');
    }
}