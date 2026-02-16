<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use Illuminate\Database\Eloquent\Collection;

interface ReportStrategyInterface
{
    /**
     * Generate report data from a collection of work orders.
     *
     * @param Collection $workOrders
     * @return array<string, mixed>
     */
    public function generate(Collection $workOrders): array;
}
