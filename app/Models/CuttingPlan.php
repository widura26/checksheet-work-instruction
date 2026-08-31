<?php

namespace App\Models;

use App\Models\Bpm;
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
    ];

    protected $appends = ['materials_data'];

    public function getMaterialsDataAttribute()
    {
        if (! $this->relationLoaded('materials')) {
            $this->load('materials.parts');
        }

        return $this->materials->map(function ($m) {
            return [
                'id' => $m->id,
                'material_id' => $m->material_id,
                'material_name' => $m->material ? $m->material->kode_material . ' - ' . $m->material->deskripsi : '',
                'material_size' => $m->material_size,
                'quantity' => $m->quantity,
                'wbs' => $m->wbs,
                'parts' => $m->parts->map(function ($p) {
                    return [
                        'part_no' => $p->part_number,
                        'cutting_size' => $p->cutting_size,
                        'qty_car' => $p->quantity_per_car,
                        'qty_lot' => $p->quantity_per_lot,
                        'part_name' => $p->part_name,
                        'remark' => $p->remark,
                    ];
                })->values()->all(),
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
        return $this->hasManyThrough(Part::class, CuttingPlanMaterial::class, 'cutting_plan_id', 'cutting_plan_material_id');
    }

    public function materials()
    {
        return $this->hasMany(CuttingPlanMaterial::class, 'cutting_plan_id');
    }

    public function material(){
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function bpm(){
        return $this->hasOne(Bpm::class, 'cutting_plan_id');
    }
}
