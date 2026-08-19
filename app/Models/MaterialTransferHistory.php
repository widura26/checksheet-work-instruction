<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransferHistory extends Model
{
    use HasFactory;

    protected $table = 'material_transfer_histories';

    protected $fillable = [
        'material_transfer_posting_id',
        'action',
        'notes',
        'user_id',
        'returned_at',
    ];

    public function items()
    {
        return $this->hasMany(MaterialTransferHistoryItem::class, 'material_transfer_history_id');
    }

    public function posting(): BelongsTo
    {
        return $this->belongsTo(MaterialtransferPosting::class, 'material_transfer_posting_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
