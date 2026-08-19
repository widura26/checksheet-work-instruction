<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $primaryKey = 'project_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'nama_project',
        'lokasi',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'project_id';
    }
    protected static function booted()
    {
        //untuk menghindari project yang tidak memiliki stok material
        static::addGlobalScope('onlyWithStock', function (Builder $builder) {
            $builder->has('projectTools');
        });
    }
    public function distribusiTools()
    {
        return $this->hasMany(DistribusiTool::class, 'project_id', 'project_id');
    }

    public function distribusiToolsDest()
    {
        return $this->hasMany(DistribusiTool::class, 'project_id_dest', 'project_id');
    }

    public function toolStocks()
    {
        return $this->hasMany(ToolProjectStock::class, 'project_id', 'project_id');
    }

    public function materialTransfersFrom()
    {
        return $this->hasMany(MaterialtransferPosting::class, 'from_project_id', 'project_id');
    }

    public function materialTransfersTo()
    {
        return $this->hasMany(MaterialtransferPosting::class, 'to_project_id', 'project_id');
    }

    public function projectTools()
    {
        return $this->belongsToMany(
            Tools::class,
            'tool_project_stocks',
            'project_id',
            'tool_id',
            'project_id',
            'id'
        )->withPivot('qty', 'posisi_ct_id');
    }
}
