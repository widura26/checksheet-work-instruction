<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Piecepart extends Model
{
    use HasFactory;

    protected $table = 'pieceparts';

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

    public function piecepartDatang(): BelongsTo
    {
        return $this->belongsTo(PiecepartDatang::class);
    }

    public function mdHeader(): BelongsTo
    {
        return $this->belongsTo(MdHeader::class);
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    public function oka(): BelongsTo
    {
        return $this->belongsTo(Oka::class);
    }
}