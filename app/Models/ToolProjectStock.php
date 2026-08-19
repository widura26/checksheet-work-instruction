<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolProjectStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'posisi_ct_id',
        'project_id',
        'qty',
    ];
    public function getQtyFormattedAttribute()
    {
        if ($this->qty === null) {
            return null;
        }

        return rtrim(rtrim(number_format($this->qty, 4, '.', ''), '0'), '.');
    }

    /**
     * Relasi ke model Tools
     */
    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id', 'id');
    }

    /**
     * Relasi ke model Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Relasi ke model Posisi_CT
     */
    public function posisiCT()
    {
        return $this->belongsTo(Posisi_CT::class, 'posisi_ct_id', 'id');
    }
}
