<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Mdr extends Model
{
    use HasFactory;

    protected $table = 'mdrs';

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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function oka(): BelongsTo
    {
        return $this->belongsTo(Oka::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
