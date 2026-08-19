<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialTransferPosting extends Model
{
    use HasFactory;

    protected $table = 'material_transfer_postings';

    protected $fillable = [
        'transfer_code',
        'from_project_id',
        'to_project_id',
        'from_posisi_ct_id',
        'to_posisi_ct_id',
        'transfer_date',
        'expected_return_date',
        'status',
        'created_by',
        'resubmit_from_id',
        'canceled_at',
        'cancel_reason',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fromProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'from_project_id', 'project_id');
    }

    public function toProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'to_project_id', 'project_id');
    }
    public function fromCt(): BelongsTo
    {
        return $this->belongsTo(Posisi_CT::class, 'from_posisi_ct_id', 'id');
    }
    public function toCt(): BelongsTo
    {
        return $this->belongsTo(Posisi_CT::class, 'to_posisi_ct_id', 'id');
    }
    public function items(): HasMany
    {
        return $this->hasMany(MaterialtransferPostingItem::class, 'material_transfer_posting_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MaterialTransferHistory::class, 'material_transfer_posting_id');
    }
}
