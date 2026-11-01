<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'name',
        'position',
        'department',
        'phone',
        'email',
        'address',
        'join_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
        ];
    }
}

