<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'item_name',
        'category',
        'quantity',
        'unit',
        'image_url', // 🌟 Added to fillable
        'min_stock_level',
        'price_per_unit',
        'status',
    ];
}