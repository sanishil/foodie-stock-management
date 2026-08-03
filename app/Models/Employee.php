<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'eid',
        'name',
        'role',
        'email',
        'phone',
        'avatar_url',
        'status',
    ];
    protected $hidden = [
        'id',
        'eid',
        'email',
        'phone',
    ];
}