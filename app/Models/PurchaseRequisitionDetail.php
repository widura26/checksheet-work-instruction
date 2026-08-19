<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PurchaseRequisitionDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisition_details';

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->public_id = (string) Str::uuid();
        });
    }

    public function pr()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id', 'id');
    }

    public function detailKerusakanTool()
    {
        return $this->belongsTo(
            DetailKerusakanTool::class,
            'detail_kerusakan_tools_id'
        );
    }
}
