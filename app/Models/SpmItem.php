<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpmItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function itemHistory()
    {
        return $this->hasMany(SpmItemsHistory::class, 'spm_item_id', 'id');
    }
}
