<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderBatch;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use Carbon\Carbon;

class AggregationService
{
    public function __construct(
        protected WorkOrderRepositoryInterface $workOrderRepository,
    ) {
    }

    /**
     * Get a daily summary of all work orders for a given date.
     *
     * @return array<string, mixed>
     */
    public function getDailySummary(Carbon $date): array
    {
        $workOrders = $this->workOrderRepository->findByDate($date->toDateString());

        $totalQuantity = 0.0;
        $totalWeight = 0.0;
        $totalPrice = 0.0;

        /** @var array<string, array<string, mixed>> $productBreakdown */
        $productBreakdown = [];

        foreach ($workOrders as $workOrder) {
            $totalQuantity += (float) $workOrder->quantity;
            $totalWeight += $workOrder->totalWeight();
            $totalPrice += (float) $workOrder->total_price;

            $productName = $workOrder->product->name ?? 'Unknown';
            $productId = (string) $workOrder->product_id;

            if (!isset($productBreakdown[$productId])) {
                $productBreakdown[$productId] = [
                    'product_name' => $productName,
                    'sku' => $workOrder->product->sku ?? '',
                    'total_quantity' => 0.0,
                    'total_weight' => 0.0,
                    'total_price' => 0.0,
                    'order_count' => 0,
                ];
            }

            $productBreakdown[$productId]['total_quantity'] += (float) $workOrder->quantity;
            $productBreakdown[$productId]['total_weight'] += $workOrder->totalWeight();
            $productBreakdown[$productId]['total_price'] += (float) $workOrder->total_price;
            $productBreakdown[$productId]['order_count']++;
        }

        return [
            'date' => $date->toDateString(),
            'total_orders' => $workOrders->count(),
            'total_quantity' => round($totalQuantity, 2),
            'total_weight' => round($totalWeight, 4),
            'total_price' => round($totalPrice, 2),
            'product_breakdown' => array_values($productBreakdown),
        ];
    }

    /**
     * Get a summary of all work orders within a specific batch.
     *
     * @return array<string, mixed>
     */
    public function getOrderSummary(OrderBatch $batch): array
    {
        $batch->load(['workOrders.product.category', 'workOrders.client']);

        $workOrders = $batch->workOrders;

        $totalQuantity = 0.0;
        $totalWeight = 0.0;
        $totalPrice = 0.0;

        /** @var array<string, array<string, mixed>> $categoryBreakdown */
        $categoryBreakdown = [];

        /** @var array<string, array<string, mixed>> $clientBreakdown */
        $clientBreakdown = [];

        foreach ($workOrders as $workOrder) {
            $totalQuantity += (float) $workOrder->quantity;
            $totalWeight += $workOrder->totalWeight();
            $totalPrice += (float) $workOrder->total_price;

            $categoryName = $workOrder->product->category->name ?? 'Uncategorized';
            $categoryId = (string) ($workOrder->product->category_id ?? 0);

            if (!isset($categoryBreakdown[$categoryId])) {
                $categoryBreakdown[$categoryId] = [
                    'category_name' => $categoryName,
                    'total_quantity' => 0.0,
                    'total_weight' => 0.0,
                    'total_price' => 0.0,
                    'order_count' => 0,
                ];
            }

            $categoryBreakdown[$categoryId]['total_quantity'] += (float) $workOrder->quantity;
            $categoryBreakdown[$categoryId]['total_weight'] += $workOrder->totalWeight();
            $categoryBreakdown[$categoryId]['total_price'] += (float) $workOrder->total_price;
            $categoryBreakdown[$categoryId]['order_count']++;

            $clientName = $workOrder->client->name ?? 'Unknown';
            $clientId = (string) $workOrder->client_id;

            if (!isset($clientBreakdown[$clientId])) {
                $clientBreakdown[$clientId] = [
                    'client_name' => $clientName,
                    'total_quantity' => 0.0,
                    'total_price' => 0.0,
                    'order_count' => 0,
                ];
            }

            $clientBreakdown[$clientId]['total_quantity'] += (float) $workOrder->quantity;
            $clientBreakdown[$clientId]['total_price'] += (float) $workOrder->total_price;
            $clientBreakdown[$clientId]['order_count']++;
        }

        return [
            'batch' => [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'name' => $batch->name,
                'type' => $batch->type->label(),
                'date' => $batch->date?->toDateString(),
                'shift' => $batch->shift,
            ],
            'totals' => [
                'total_orders' => $workOrders->count(),
                'total_quantity' => round($totalQuantity, 2),
                'total_weight' => round($totalWeight, 4),
                'total_price' => round($totalPrice, 2),
            ],
            'category_breakdown' => array_values($categoryBreakdown),
            'client_breakdown' => array_values($clientBreakdown),
            'status_breakdown' => $batch->statusBreakdown(),
        ];
    }
}
