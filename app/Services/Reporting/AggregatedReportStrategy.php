<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;

class AggregatedReportStrategy implements ReportStrategyInterface
{
    /**
     * Generate an aggregated report: totals grouped by Product -> Category.
     *
     * @param Collection $workOrders
     * @return array<string, mixed>
     */
    public function generate(Collection $workOrders): array
    {
        $totalQuantity = 0.0;
        $totalWeight = 0.0;
        $totalPrice = 0.0;

        /** @var array<string, array<string, mixed>> $byCategory */
        $byCategory = [];

        /** @var array<string, array<string, mixed>> $byProduct */
        $byProduct = [];

        /** @var array<string, array<string, mixed>> $materialsSummary */
        $materialsSummary = [];

        /** @var WorkOrder $workOrder */
        foreach ($workOrders as $workOrder) {
            $workOrder->loadMissing(['product.category']);

            $quantity = (float) $workOrder->quantity;
            $weight = $workOrder->totalWeight();
            $price = (float) $workOrder->total_price;

            $totalQuantity += $quantity;
            $totalWeight += $weight;
            $totalPrice += $price;

            // --- Group by Product ---
            $productId = (string) $workOrder->product_id;
            $productName = $workOrder->product->name ?? 'Unknown';

            if (!isset($byProduct[$productId])) {
                $byProduct[$productId] = [
                    'product_id' => $workOrder->product_id,
                    'product_name' => $productName,
                    'sku' => $workOrder->product->sku ?? '',
                    'uom' => $workOrder->product->uom?->label() ?? '',
                    'total_quantity' => 0.0,
                    'effective_quantity' => 0.0,
                    'total_weight' => 0.0,
                    'total_price' => 0.0,
                    'order_count' => 0,
                ];
            }

            $byProduct[$productId]['total_quantity'] += $quantity;
            $byProduct[$productId]['effective_quantity'] += $workOrder->effectiveQuantity();
            $byProduct[$productId]['total_weight'] += $weight;
            $byProduct[$productId]['total_price'] += $price;
            $byProduct[$productId]['order_count']++;

            // --- Group by Category ---
            $categoryId = (string) ($workOrder->product->category_id ?? 0);
            $categoryName = $workOrder->product->category->name ?? 'Uncategorized';

            if (!isset($byCategory[$categoryId])) {
                $byCategory[$categoryId] = [
                    'category_id' => $workOrder->product->category_id ?? null,
                    'category_name' => $categoryName,
                    'total_quantity' => 0.0,
                    'total_weight' => 0.0,
                    'total_price' => 0.0,
                    'order_count' => 0,
                    'products' => [],
                ];
            }

            $byCategory[$categoryId]['total_quantity'] += $quantity;
            $byCategory[$categoryId]['total_weight'] += $weight;
            $byCategory[$categoryId]['total_price'] += $price;
            $byCategory[$categoryId]['order_count']++;

            if (!in_array($productName, $byCategory[$categoryId]['products'], true)) {
                $byCategory[$categoryId]['products'][] = $productName;
            }

            // --- Materials summary from product specifications ---
            $specs = $workOrder->product->specifications ?? [];
            $material = $specs['material'] ?? null;

            if ($material !== null) {
                $materialKey = strtolower((string) $material);

                if (!isset($materialsSummary[$materialKey])) {
                    $materialsSummary[$materialKey] = [
                        'material' => (string) $material,
                        'total_quantity' => 0.0,
                        'total_weight' => 0.0,
                        'order_count' => 0,
                    ];
                }

                $materialsSummary[$materialKey]['total_quantity'] += $quantity;
                $materialsSummary[$materialKey]['total_weight'] += $weight;
                $materialsSummary[$materialKey]['order_count']++;
            }
        }

        // Round all aggregated values
        foreach ($byProduct as &$product) {
            $product['total_quantity'] = round($product['total_quantity'], 2);
            $product['effective_quantity'] = round($product['effective_quantity'], 2);
            $product['total_weight'] = round($product['total_weight'], 4);
            $product['total_price'] = round($product['total_price'], 2);
        }
        unset($product);

        foreach ($byCategory as &$category) {
            $category['total_quantity'] = round($category['total_quantity'], 2);
            $category['total_weight'] = round($category['total_weight'], 4);
            $category['total_price'] = round($category['total_price'], 2);
        }
        unset($category);

        foreach ($materialsSummary as &$material) {
            $material['total_quantity'] = round($material['total_quantity'], 2);
            $material['total_weight'] = round($material['total_weight'], 4);
        }
        unset($material);

        return [
            'report_type' => 'aggregated',
            'generated_at' => now()->toDateTimeString(),
            'totals' => [
                'total_orders' => $workOrders->count(),
                'total_quantity' => round($totalQuantity, 2),
                'total_weight' => round($totalWeight, 4),
                'total_price' => round($totalPrice, 2),
            ],
            'by_product' => array_values($byProduct),
            'by_category' => array_values($byCategory),
            'materials_needed' => array_values($materialsSummary),
        ];
    }
}
