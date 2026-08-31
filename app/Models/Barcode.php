<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Barcode extends Model
{
    use HasFactory;

    protected $table = 'barcodes';

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

    public function wiTrn()
    {
        return $this->belongsTo(WiTrn::class, 'wi_trn_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cuttingPlan()
    {
        return $this->belongsTo(CuttingPlan::class, 'cutting_plan_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}
