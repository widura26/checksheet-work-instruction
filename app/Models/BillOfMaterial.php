<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillOfMaterial extends Model
{
    use HasFactory;

    protected $table = 'bill_of_materials';
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

    public function revisions()
    {
        return $this->hasMany(BillOfMaterialRevision::class, 'bom_id');
    }

    public function latestRevision()
    {
        return $this->hasOne(
            BillOfMaterialRevision::class,
            'bom_id'
        )->latestOfMany('created_at');
    }
}
