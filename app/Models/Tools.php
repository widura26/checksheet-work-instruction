<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tools extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'public_id'];

    protected $cast = [
        'tanggal_ditambahkan' => 'datetime',
    ];

    public function posisiCT()
    {
        return $this->belongsTo(Posisi_CT::class, 'posisi_ct_id', 'id');
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

    public function updateStockTools()
    {
        return $this->hasMany(UpdateStockTool::class, 'tool_id', 'id')->orderBy('tanggal_update', 'desc');
    }

    public function penguranganTools()
    {
        return $this->hasMany(PenguranganTool::class, 'tool_id', 'id')->orderBy('tanggal_pengurangan', 'desc');
    }

    public function daftarDistribusiTools()
    {
        return $this->hasMany(DaftarDistribusiTool::class, 'tool_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'bagian_id', 'id');
    }

    public function departement()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id');
    }

    public function stockToolUU()
    {
        return $this->hasOne(ToolStockBebas::class, 'tool_id', 'id');
    }

    public function projectStocks()
    {
        return $this->hasMany(ToolProjectStock::class, 'tool_id', 'id');
    }

    public function stockProject()
    {
        return $this->belongsToMany(
            Project::class,
            'tool_project_stocks',
            'tool_id',
            'project_id',
            'id',
            'project_id'
        )->withPivot('qty', 'posisi_ct_id');
    }

    public function materialTransferPostingItems()
    {
        return $this->hasMany(MaterialtransferPostingItem::class, 'from_tool_id', 'id');
    }
}
