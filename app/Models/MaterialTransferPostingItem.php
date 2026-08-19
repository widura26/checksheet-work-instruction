<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransferPostingItem extends Model
{
    use HasFactory;

    protected $table = 'material_transfer_posting_items';

    protected $fillable = [
        'material_transfer_posting_id',
        'from_tool_id',
        'to_tool_id',
        'from_posisi_ct_id',
        'from_project_id',
        'qty',
        'returned_qty',
    ];

    public function posting(): BelongsTo
    {
        return $this->belongsTo(MaterialtransferPosting::class, 'material_transfer_posting_id');
    }

    public function fromTool(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'from_tool_id');
    }

    public function toTool(): BelongsTo
    {
        return $this->belongsTo(Tools::class, 'to_tool_id');
    }

    public function fromProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'from_project_id', 'project_id');
    }
}
