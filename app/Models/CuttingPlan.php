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
        'pieceparts' => 'array',
        'date_issued' => 'date',
        'date_revised' => 'date',
        'date_of_bpm' => 'date',
        'quantity' => 'integer',
        'qty_lot' => 'integer',
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
