<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkEstimateItem extends Model
{
    protected $fillable = [
        'work_estimate_id',
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function workEstimate(): BelongsTo
    {
        return $this->belongsTo(WorkEstimate::class);
    }
}
