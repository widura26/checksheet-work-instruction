<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateStockToolProject extends Model
{
    use HasFactory;

    protected $table = 'update_stock_tool_projects';

    protected $guarded = ['id'];

    public function updateStock()
    {
        return $this->belongsTo(UpdateStockTool::class, 'update_stock_tools_id', 'id');
    }
}
