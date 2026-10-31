<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'total_stock',
        'notes',
    ];

    protected $casts = [
        'total_stock' => 'integer',
    ];

    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    public function activeProjectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class)->where('status', 'allocated');
    }

    public function getUsedStockAttribute(): int
    {
        if (array_key_exists('used_stock', $this->attributes)) {
            return (int) $this->attributes['used_stock'];
        }

        if ($this->relationLoaded('activeProjectItems')) {
            return (int) $this->activeProjectItems->sum('quantity');
        }

        if ($this->relationLoaded('projectItems')) {
            return (int) $this->projectItems->where('status', 'allocated')->sum('quantity');
        }

        return (int) $this->projectItems()->where('status', 'allocated')->sum('quantity');
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->total_stock - $this->used_stock);
    }
}

