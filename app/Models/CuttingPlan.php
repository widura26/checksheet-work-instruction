<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CuttingPlan extends Model
{
    use HasFactory;

    protected $table = 'cutting_plans';

    protected $guarded = ['id'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'date_issued' => 'date',
        'date_revised' => 'date',
        'date_of_bpm' => 'date',
        'quantity' => 'integer',
        'qty_lot' => 'integer',
    ];

    protected $appends = ['pieceparts'];

    public function getPiecepartsAttribute()
    {
        if (! $this->relationLoaded('parts')) {
            $this->load('parts');
        }

        return $this->parts->map(function ($p) {
            return [
                'part_no' => $p->part_number,
                'cutting_size' => $p->cutting_size,
                'qty_car' => $p->quantity_per_car,
                'qty_lot' => $p->quantity_per_lot,
                'part_name' => $p->part_name,
                'remark' => $p->remark,
            ];
        })->values()->all();
    }

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

    public function mdHeader()
    {
        return $this->belongsTo(MdHeader::class, 'md_header_id');
    }

    public function exp()
    {
        return $this->belongsTo(Exp::class, 'exp_id');
    }

    public function bpmCp()
    {
        return $this->belongsTo(BpmCp::class, 'bpm_cp_id');
    }

    public function expCp()
    {
        return $this->belongsTo(ExpCp::class, 'exp_cp_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function process(){
        return $this->belongsTo(Proses::class, 'process_id');
    }

    public function parts()
    {
        return $this->hasMany(Part::class, 'cutting_plan_id');
    }

    public function material(){
        return $this->belongsTo(Material::class, 'material_id');
    }
}
