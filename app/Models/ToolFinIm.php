<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ToolFinIm extends Model
{
    use HasFactory;

    protected $table = 'tool_fins_ims';

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

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}