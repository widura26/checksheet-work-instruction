<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolStockBebas extends Model
{
    use HasFactory;

    protected $table = 'tool_stock_bebas';

    public function tool()
    {
        return $this->belongsTo(Tools::class, 'kode_material', 'kode_material');
    }
}
