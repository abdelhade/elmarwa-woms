<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BatchType;
use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'type',
        'name',
        'date',
        'shift',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => BatchType::class,
            'date' => 'date',
            'status' => WorkOrderStatus::class,
        ];
    }

    /**
     * @return HasMany<WorkOrder, $this>
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the total quantity across all work orders in this batch.
     */
    public function totalQuantity(): float
    {
        return (float) $this->workOrders()->sum('quantity');
    }

    /**
     * Get the total price across all work orders in this batch.
     */
    public function totalPrice(): float
    {
        return (float) $this->workOrders()->sum('total_price');
    }

    /**
     * Get the count of work orders per status.
     *
     * @return array<string, int>
     */
    public function statusBreakdown(): array
    {
        $breakdown = [];

        foreach (WorkOrderStatus::cases() as $status) {
            $count = $this->workOrders()->where('status', $status->value)->count();
            if ($count > 0) {
                $breakdown[$status->label()] = $count;
            }
        }

        return $breakdown;
    }
}
