<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\BatchType;
use App\Models\OrderBatch;
use Illuminate\Database\Eloquent\Collection;

interface OrderBatchRepositoryInterface
{
    /**
     * @return Collection<int, OrderBatch>
     */
    public function all(): Collection;

    public function find(int $id): ?OrderBatch;

    public function findOrFail(int $id): OrderBatch;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): OrderBatch;

    /**
     * @param array<string, mixed> $data
     */
    public function update(OrderBatch $orderBatch, array $data): OrderBatch;

    public function delete(OrderBatch $orderBatch): bool;

    /**
     * @return Collection<int, OrderBatch>
     */
    public function findByType(BatchType $type): Collection;

    /**
     * @return Collection<int, OrderBatch>
     */
    public function findByDate(string $date): Collection;

    public function findWithWorkOrders(int $id): ?OrderBatch;
}
