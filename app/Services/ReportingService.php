<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Reporting\ReportStrategyInterface;
use Illuminate\Database\Eloquent\Collection;

class ReportingService
{
    protected ReportStrategyInterface $strategy;

    public function __construct(ReportStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Set the reporting strategy.
     */
    public function setStrategy(ReportStrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Get the current strategy.
     */
    public function getStrategy(): ReportStrategyInterface
    {
        return $this->strategy;
    }

    /**
     * Generate a report using the current strategy.
     *
     * @param Collection $workOrders
     * @return array<string, mixed>
     */
    public function generate(Collection $workOrders): array
    {
        return $this->strategy->generate($workOrders);
    }
}
