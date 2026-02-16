<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BatchType;
use App\Models\OrderBatch;
use App\Repositories\Contracts\OrderBatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderBatchRepository implements OrderBatchRepositoryInterface
{
    public function __construct(
        protected OrderBatch $model,
    ) {
    }

    /**
     * @return Collection<int, OrderBatch>
     */
    public function all(): Collection
    {
        return $this->model
            ->with('workOrders')
            ->orderByDesc('created_at')
            ->get();
    }

    public function find(int $id): ?OrderBatch
    {
        return $this->model
            ->with('workOrders')
            ->find($id);
    }

    public function findOrFail(int $id): OrderBatch
    {
        return $this->model
            ->with('workOrders')
            ->findOrFail($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): OrderBatch
    {
        $batch = $this->model->newInstance($data);
        $batch->save();

        return $batch->load('workOrders');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(OrderBatch $orderBatch, array $data): OrderBatch
    {
        $orderBatch->update($data);

        return $orderBatch->fresh('workOrders');
    }

    public function delete(OrderBatch $orderBatch): bool
    {
        return (bool) $orderBatch->delete();
    }

    /**
     * @return Collection<int, OrderBatch>
     */
    public function findByType(BatchType $type): Collection
    {
        return $this->model
            ->with('workOrders')
            ->where('type', $type->value)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, OrderBatch>
     */
    public function findByDate(string $date): Collection
    {
        return $this->model
            ->with('workOrders')
            ->whereDate('date', $date)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findWithWorkOrders(int $id): ?OrderBatch
    {
        return $this->model
            ->with(['workOrders.client', 'workOrders.product'])
            ->find($id);
    }
}
