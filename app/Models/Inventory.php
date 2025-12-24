<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'quantity',
        'last_updated',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'last_updated' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeUpdatedBefore(Builder $query, \DateTimeInterface $threshold): Builder
    {
        return $query->whereNotNull('last_updated')
            ->where('last_updated', '<', $threshold);
    }
}
