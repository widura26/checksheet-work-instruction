<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posisi_CT extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posisi__c_t_s';

    protected $guarded = ['id'];

    public function tools()
    {
        return $this->hasMany(Tools::class, 'posisi_ct_id', 'id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
}
