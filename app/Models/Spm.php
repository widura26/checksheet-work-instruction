<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spm extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function posisiCt()
    {
        return $this->belongsTo(Posisi_CT::class, 'posisi_ct_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(SpmItem::class);
    }
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
