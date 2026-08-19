<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DistribusiTool extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'public_id'];

    protected $casts = [
        'tanggal_pinjam_diserahkan' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getRouteKeyName()
    {
        return 'public_id';
    }

    public function daftarDistribusiTool()
    {
        return $this->hasMany(DaftarDistribusiTool::class, 'distribusi_tools_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function projectDest()
    {
        return $this->belongsTo(Project::class, 'project_id_dest', 'project_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->public_id = (string) Str::uuid();
        });
    }
}
