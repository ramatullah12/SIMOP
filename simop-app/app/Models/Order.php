<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'client_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'event_name',
        'event_location',
        'event_date',
        'description',
        'estimated_budget',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'estimated_budget' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
