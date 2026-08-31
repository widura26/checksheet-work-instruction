<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CuttingPlanMaterial extends Model
{
    use HasFactory;

    protected $table = 'cutting_plan_materials';
    protected $guarded = ['id'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function cuttingPlan()
    {
        return $this->belongsTo(CuttingPlan::class, 'cutting_plan_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function parts()
    {
        return $this->hasMany(Part::class, 'cutting_plan_material_id');
    }
}
