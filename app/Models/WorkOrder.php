<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'product_id',
        'order_batch_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'specifications',
        'due_date',
        'notes',
        'employee_id',
        'operator_id',
        'supervisor_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'client_id' => 'integer',
            'product_id' => 'integer',
            'order_batch_id' => 'integer',
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'status' => WorkOrderStatus::class,
            'specifications' => 'array',
            'due_date' => 'date',
            'employee_id' => 'integer',
            'operator_id' => 'integer',
            'supervisor_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<OrderBatch, $this>
     */
    public function orderBatch(): BelongsTo
    {
        return $this->belongsTo(OrderBatch::class);
    }

    /**
     * @return HasMany<WorkOrderDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(WorkOrderDetail::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    /**
     * Calculate the total price based on quantity and unit price.
     */
    public function calculateTotalPrice(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price, 2);
    }

    /**
     * Calculate the effective quantity including product waste rate.
     */
    public function effectiveQuantity(): float
    {
        if ($this->product === null) {
            return (float) $this->quantity;
        }

        return $this->product->effectiveQuantity((float) $this->quantity);
    }

    /**
     * Calculate the total weight for this work order.
     */
    public function totalWeight(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        return $this->product->totalWeight((float) $this->quantity);
    }
}
