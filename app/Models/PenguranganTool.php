<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenguranganTool extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'public_id'];

    protected $casts = [
        'tanggal_pengurangan' => 'datetime',
    ];

    /**
     * Get the tool that was reduced.
     */
    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function  project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
    public function posisiCt()
    {
        return $this->belongsTo(Posisi_CT::class, 'posisi_ct_id');
    }
    public function detailKerusakanTool()
    {
        return $this->belongsTo(DetailKerusakanTool::class, 'detail_kerusakan_tools_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->public_id = (string) Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'public_id';
    }
}
