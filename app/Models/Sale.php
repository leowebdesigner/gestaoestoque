<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'total_amount',
        'total_cost',
        'total_profit',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'status' => SaleStatus::class,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeBetweenDates(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('sales.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('sales.created_at', '<=', $endDate);
        }

        return $query;
    }

    public function scopeWithProductSku(Builder $query, ?string $sku): Builder
    {
        if (!$sku) {
            return $query;
        }

        return $query->whereHas('items.product', function (Builder $builder) use ($sku): void {
            $builder->where('sku', $sku);
        });
    }
}
