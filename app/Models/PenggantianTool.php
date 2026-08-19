<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggantianTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'qty_diganti',
        'departemen_id',
        'bagian_id',
        'unit_id',
        'tanggal_penggantian',
        'status',
        'keterangan',
        'file'
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tool_id');
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
