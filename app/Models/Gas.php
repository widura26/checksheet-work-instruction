<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gas extends Model
{
    use HasFactory;

    protected $table = 'gases';

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

    public function material()
    {
        return $this->belongsTo(WiTrnBpm::class, 'material_id');
    }

    public function bpm()
    {
        return $this->belongsTo(Bpm::class, 'bpm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
