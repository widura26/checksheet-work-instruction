<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransferHistoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_transfer_history_id',
        'tool_id',
        'qty_returned',
        'user_id',
    ];

    public function history(): BelongsTo
    {
        return $this->belongsTo(MaterialTransferHistory::class, 'material_transfer_history_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'tool_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
