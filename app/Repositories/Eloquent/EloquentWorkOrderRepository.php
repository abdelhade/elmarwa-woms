<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentWorkOrderRepository implements WorkOrderRepositoryInterface
{
    public function __construct(
        protected WorkOrder $model,
    ) {
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function all(): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function find(int $id): ?WorkOrder
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->find($id);
    }

    public function findOrFail(int $id): WorkOrder
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->findOrFail($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): WorkOrder
    {
        $workOrder = $this->model->newInstance($data);
        $workOrder->save();

        return $workOrder->load(['client', 'product', 'orderBatch']);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(WorkOrder $workOrder, array $data): WorkOrder
    {
        $workOrder->update($data);

        return $workOrder->fresh(['client', 'product', 'orderBatch']);
    }

    public function delete(WorkOrder $workOrder): bool
    {
        return (bool) $workOrder->delete();
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByStatus(WorkOrderStatus $status): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->where('status', $status->value)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByClient(int $clientId): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->where('client_id', $clientId)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByBatch(int $batchId): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->where('order_batch_id', $batchId)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByDate(string $date): Collection
    {
        return $this->model
            ->with(['client', 'product', 'orderBatch'])
            ->whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->get();
    }
}
