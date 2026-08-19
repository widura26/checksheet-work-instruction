<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Material extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'materials'; // Menyesuaikan nama tabel dengan migrasi

    protected $primaryKey = 'id'; // Menyesuaikan primary key

    public $incrementing = true;

    protected $keyType = 'string';

    protected $fillable = [
        'kode_material',
        'nama',
        'spek',
        'project',
        'jumlah',
        'satuan',
        'lokasi',
        'status',
        'foto',

    ];

    // Tentukan atribut yang ingin dilog
    protected static $logAttributes = ['*']; // Menggunakan '*' untuk semua atribut

    // Menentukan log name
    protected static $logName = 'Material';

    // Log hanya perubahan yang terjadi
    protected static $logOnlyDirty = true;

    // Menampilkan log perubahan atribut
    protected static $logAttributesToIgnore = ['updated_at'];

    // Implementasikan getActivitylogOptions
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log semua atribut
            ->useLogName(self::$logName)
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    // Custom description untuk log
    public function getDescriptionForEvent(string $eventName): string
    {
        return 'The '.self::$logName." with ID {$this->id} has been {$eventName}";
    }

    public function projectMaterials()
    {
        return $this->hasMany(project_material::class, 'material_id');
    }
}
