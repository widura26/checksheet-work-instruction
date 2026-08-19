<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarcodeDetail extends Model
{
    use HasFactory;

    protected $table = 'barcode_details';

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

    public function barcode()
    {
        return $this->belongsTo(Barcode::class);
    }

    public function process()
    {
        return $this->belongsTo(Proses::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}