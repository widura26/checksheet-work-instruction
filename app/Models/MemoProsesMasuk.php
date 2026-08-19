<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MemoProsesMasuk extends Model
{
    use HasFactory;

    protected $table = 'memo_proses_masuks';

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

    public function material(): BelongsTo
    {
        return $this->belongsTo(WiTrnBpm::class);
    }

    public function bpm(): BelongsTo
    {
        return $this->belongsTo(Bpm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}