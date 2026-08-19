<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MdHeader extends Model
{
    use HasFactory;

    protected $table = 'md_headers';

    protected $guarded = ['id'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function drawing()
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class, 'material_id');
    }
}
