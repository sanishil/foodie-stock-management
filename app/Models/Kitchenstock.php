<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitchenstock extends Model
{
    use HasFactory;
    protected $table = 'kitchenstocks';
    protected $fillable = [
        'id',
        'eid',
        'ingredient_name',
        'quantity',
        'unit',
        'minimum_stock_alert',
        'request_item',
        'request_to_admin',
        'status',
        'user',
    ];
}
