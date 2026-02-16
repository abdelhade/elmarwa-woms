<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\WorkOrderStatus;
use App\Models\OrderBatch;
use App\Models\Product;
use App\Models\WorkOrder;
use App\Repositories\Contracts\OrderBatchRepositoryInterface;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderProcessingService
{
    public function __construct(
        protected WorkOrderRepositoryInterface $workOrderRepository,
        protected OrderBatchRepositoryInterface $orderBatchRepository,
    ) {
    }

    /**
     * Create a new work order.
     *
     * @param array<string, mixed> $data
     */
    public function createWorkOrder(array $data): WorkOrder
    {
        $product = Product::findOrFail($data['product_id']);

        if (!isset($data['order_number'])) {
            $data['order_number'] = $this->generateOrderNumber();
        }

        if (!isset($data['unit_price'])) {
            $data['unit_price'] = (float) $product->unit_price;
        }

        $data['total_price'] = round(
            (float) $data['quantity'] * (float) $data['unit_price'],
            2
        );

        if (!isset($data['status'])) {
            $data['status'] = WorkOrderStatus::Pending->value;
        }

        return $this->workOrderRepository->create($data);
    }

    /**
     * Create a work order with multiple detail lines.
     *
     * @param array<string, mixed> $data
     */
    public function createWorkOrderWithDetails(array $data): WorkOrder
    {
        return \DB::transaction(function () use ($data) {
            $orderData = [
                'order_number' => $this->generateOrderNumber(),
                'client_id' => $data['client_id'],
                'product_id' => $data['details'][0]['product_id'] ?? null,
                'order_batch_id' => $data['order_batch_id'] ?? null,
                'quantity' => 0,
                'unit_price' => 0,
                'total_price' => 0,
                'status' => WorkOrderStatus::Pending->value,
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            $workOrder = $this->workOrderRepository->create($orderData);

            $grandTotal = 0;
            $totalQty = 0;

            foreach ($data['details'] as $detail) {
                $lineTotal = round((float) $detail['quantity'] * (float) $detail['unit_price'], 2);
                $workOrder->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total_price' => $lineTotal,
                    'attributes' => $detail['attributes'] ?? [],
                ]);
                $grandTotal += $lineTotal;
                $totalQty += (float) $detail['quantity'];
            }

            $workOrder->update([
                'total_price' => $grandTotal,
                'quantity' => $totalQty,
            ]);

            return $workOrder;
        });
    }

    /**
     * Update a work order with multiple detail lines.
     *
     * @param array<string, mixed> $data
     */
    public function updateWorkOrderWithDetails(WorkOrder $workOrder, array $data): WorkOrder
    {
        return \DB::transaction(function () use ($workOrder, $data) {
            $updateData = [
                'client_id' => $data['client_id'],
                'order_batch_id' => $data['order_batch_id'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? $workOrder->status->value,
            ];

            $workOrder->update($updateData);

            // Delete old details and add new ones
            $workOrder->details()->delete();

            $grandTotal = 0;
            $totalQty = 0;

            foreach ($data['details'] as $detail) {
                $lineTotal = round((float) $detail['quantity'] * (float) $detail['unit_price'], 2);
                $workOrder->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total_price' => $lineTotal,
                    'attributes' => $detail['attributes'] ?? [],
                ]);
                $grandTotal += $lineTotal;
                $totalQty += (float) $detail['quantity'];
            }

            // Sync main order totals
            $workOrder->update([
                'total_price' => $grandTotal,
                'quantity' => $totalQty,
                'product_id' => $data['details'][0]['product_id'] ?? null,
            ]);

            return $workOrder->load('details.product');
        });
    }

    /**
     * Update the status of a work order following allowed transitions.
     *
     * @throws InvalidArgumentException
     */
    public function updateStatus(WorkOrder $workOrder, WorkOrderStatus $newStatus): WorkOrder
    {
        $currentStatus = $workOrder->status;

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot transition from "%s" to "%s". Allowed transitions: %s',
                    $currentStatus->label(),
                    $newStatus->label(),
                    implode(', ', array_map(
                        fn (WorkOrderStatus $s): string => $s->label(),
                        $currentStatus->allowedTransitions()
                    ))
                )
            );
        }

        return $this->workOrderRepository->update($workOrder, [
            'status' => $newStatus->value,
        ]);
    }

    /**
     * Assign a work order to a batch.
     */
    public function assignToBatch(WorkOrder $workOrder, OrderBatch $batch): WorkOrder
    {
        return $this->workOrderRepository->update($workOrder, [
            'order_batch_id' => $batch->id,
        ]);
    }

    /**
     * Create a new order batch.
     *
     * @param array<string, mixed> $data
     */
    public function createOrderBatch(array $data): OrderBatch
    {
        if (!isset($data['batch_number'])) {
            $data['batch_number'] = $this->generateBatchNumber();
        }

        if (!isset($data['status'])) {
            $data['status'] = WorkOrderStatus::Pending->value;
        }

        return $this->orderBatchRepository->create($data);
    }

    /**
     * Update an existing work order.
     *
     * @param array<string, mixed> $data
     */
    public function updateWorkOrder(WorkOrder $workOrder, array $data): WorkOrder
    {
        if (isset($data['quantity']) || isset($data['unit_price'])) {
            $quantity = (float) ($data['quantity'] ?? $workOrder->quantity);
            $unitPrice = (float) ($data['unit_price'] ?? $workOrder->unit_price);
            $data['total_price'] = round($quantity * $unitPrice, 2);
        }

        return $this->workOrderRepository->update($workOrder, $data);
    }

    /**
     * Duplicate an existing work order.
     */
    public function duplicateWorkOrder(WorkOrder $workOrder): WorkOrder
    {
        return \DB::transaction(function () use ($workOrder) {
            $workOrder->load('details');
            
            $newOrder = $workOrder->replicate();
            $newOrder->order_number = $this->generateOrderNumber();
            $newOrder->status = WorkOrderStatus::Pending->value;
            $newOrder->created_at = now();
            $newOrder->due_date = now()->addDays(7); // Default to a week from now for duplicates
            $newOrder->save();

            foreach ($workOrder->details as $detail) {
                $newDetail = $detail->replicate();
                $newDetail->work_order_id = $newOrder->id;
                $newDetail->produced_quantity = 0;
                $newDetail->save();
            }

            return $newOrder;
        });
    }

    /**
     * Generate a unique order number.
     */
    protected function generateOrderNumber(): string
    {
        return 'WO-' . strtoupper(Str::random(8));
    }

    /**
     * Generate a unique batch number.
     */
    protected function generateBatchNumber(): string
    {
        return 'BATCH-' . strtoupper(Str::random(8));
    }
}
