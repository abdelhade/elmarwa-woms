<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'uom',
        'lead_time_days',
        'waste_rate',
        'unit_weight',
        'unit_price',
        'specifications',
        'description',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'uom' => UnitOfMeasure::class,
            'lead_time_days' => 'integer',
            'waste_rate' => 'decimal:2',
            'unit_weight' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'specifications' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<WorkOrder, $this>
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Calculate the effective quantity including waste.
     */
    public function effectiveQuantity(float $baseQuantity): float
    {
        $wasteMultiplier = 1 + ((float) $this->waste_rate / 100);

        return round($baseQuantity * $wasteMultiplier, 2);
    }

    /**
     * Calculate total weight for a given quantity.
     */
    public function totalWeight(float $quantity): float
    {
        return round($quantity * (float) $this->unit_weight, 4);
    }
}
