<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bapp extends Model
{
    protected $fillable = [
        'production_id',
        'created_by',
        'bapp_number',
        'completion_date',
        'location',
        'work_result',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'completion_date' => 'date',
        ];
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}