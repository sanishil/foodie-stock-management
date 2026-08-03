<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'deliverys';
    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_name',
        'delivery_address',
        'driver_name',
        'driver_phone',
        'items',
        'total',
        'status',
    ];
}