<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Client $client): void {
            if ($client->credit_limit === null || $client->credit_limit === '') {
                $client->credit_limit = 0;
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company',
        'tax_number',
        'credit_limit',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
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
     * Calculate the total outstanding amount from active work orders.
     */
    public function outstandingAmount(): float
    {
        return (float) $this->workOrders()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum('total_price');
    }

    /**
     * Check if the client can place a new order of the given amount.
     */
    public function canPlaceOrder(float $amount): bool
    {
        if ((float) $this->credit_limit <= 0) {
            return true;
        }

        return ($this->outstandingAmount() + $amount) <= (float) $this->credit_limit;
    }
}
