<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'vendor_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'service_type',
        'status',
        'notes',
    ];
}