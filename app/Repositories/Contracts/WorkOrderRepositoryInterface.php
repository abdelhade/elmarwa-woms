<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;

interface WorkOrderRepositoryInterface
{
    /**
     * @return Collection<int, WorkOrder>
     */
    public function all(): Collection;

    public function find(int $id): ?WorkOrder;

    public function findOrFail(int $id): WorkOrder;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): WorkOrder;

    /**
     * @param array<string, mixed> $data
     */
    public function update(WorkOrder $workOrder, array $data): WorkOrder;

    public function delete(WorkOrder $workOrder): bool;

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByStatus(WorkOrderStatus $status): Collection;

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByClient(int $clientId): Collection;

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByBatch(int $batchId): Collection;

    /**
     * @return Collection<int, WorkOrder>
     */
    public function findByDate(string $date): Collection;
}
