<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'public_id';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->public_id = (string) Str::uuid();
        });
    }

    public function details()
    {
        return $this->hasMany(PurchaseRequisitionDetail::class, 'purchase_requisition_id', 'id');
    }
}
