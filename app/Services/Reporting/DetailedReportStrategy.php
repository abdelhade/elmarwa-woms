<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;

class DetailedReportStrategy implements ReportStrategyInterface
{
    /**
     * Generate a detailed report: every order with its specific attributes and client data.
     *
     * @param Collection $workOrders
     * @return array<string, mixed>
     */
    public function generate(Collection $workOrders): array
    {
        $orders = [];

        /** @var WorkOrder $workOrder */
        foreach ($workOrders as $workOrder) {
            $workOrder->loadMissing(['client', 'product.category', 'orderBatch']);

            $orders[] = [
                'order' => [
                    'id' => $workOrder->id,
                    'order_number' => $workOrder->order_number,
                    'status' => $workOrder->status->label(),
                    'quantity' => (float) $workOrder->quantity,
                    'effective_quantity' => $workOrder->effectiveQuantity(),
                    'unit_price' => (float) $workOrder->unit_price,
                    'total_price' => (float) $workOrder->total_price,
                    'total_weight' => $workOrder->totalWeight(),
                    'specifications' => $workOrder->specifications,
                    'due_date' => $workOrder->due_date?->toDateString(),
                    'notes' => $workOrder->notes,
                    'created_at' => $workOrder->created_at?->toDateTimeString(),
                ],
                'client' => [
                    'id' => $workOrder->client->id ?? null,
                    'name' => $workOrder->client->name ?? 'N/A',
                    'email' => $workOrder->client->email ?? null,
                    'phone' => $workOrder->client->phone ?? null,
                    'company' => $workOrder->client->company ?? null,
                ],
                'product' => [
                    'id' => $workOrder->product->id ?? null,
                    'name' => $workOrder->product->name ?? 'N/A',
                    'sku' => $workOrder->product->sku ?? '',
                    'uom' => $workOrder->product->uom?->label() ?? '',
                    'category' => $workOrder->product->category->name ?? 'N/A',
                    'unit_weight' => (float) ($workOrder->product->unit_weight ?? 0),
                    'waste_rate' => (float) ($workOrder->product->waste_rate ?? 0),
                    'lead_time_days' => $workOrder->product->lead_time_days ?? 0,
                    'specifications' => $workOrder->product->specifications,
                ],
                'batch' => $workOrder->orderBatch ? [
                    'id' => $workOrder->orderBatch->id,
                    'batch_number' => $workOrder->orderBatch->batch_number,
                    'name' => $workOrder->orderBatch->name,
                ] : null,
            ];
        }

        return [
            'report_type' => 'detailed',
            'generated_at' => now()->toDateTimeString(),
            'total_orders' => count($orders),
            'orders' => $orders,
        ];
    }
}
