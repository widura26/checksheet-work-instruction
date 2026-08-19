<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BpmIm extends Model
{
    use HasFactory;

    protected $table = 'bpm_ims';

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

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class, 'material_id');
    }

    public function wiTrn()
    {
        return $this->belongsTo(WiTrn::class, 'wi_trn_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
