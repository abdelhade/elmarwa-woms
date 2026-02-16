<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderBatchRepositoryInterface;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use App\Services\AggregationService;
use App\Services\Reporting\AggregatedReportStrategy;
use App\Services\Reporting\DetailedReportStrategy;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected WorkOrderRepositoryInterface $workOrderRepository,
        protected OrderBatchRepositoryInterface $orderBatchRepository,
        protected AggregationService $aggregationService,
        protected ReportingService $reportingService,
    ) {
    }

    public function detailed(Request $request): JsonResponse
    {
        $workOrders = $this->resolveWorkOrders($request);

        $this->reportingService->setStrategy(new DetailedReportStrategy());

        $report = $this->reportingService->generate($workOrders);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function aggregated(Request $request): JsonResponse
    {
        $workOrders = $this->resolveWorkOrders($request);

        $this->reportingService->setStrategy(new AggregatedReportStrategy());

        $report = $this->reportingService->generate($workOrders);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function dailySummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = isset($validated['date'])
            ? Carbon::parse($validated['date'])
            : Carbon::today();

        $summary = $this->aggregationService->getDailySummary($date);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function batchSummary(int $id): JsonResponse
    {
        $batch = $this->orderBatchRepository->findOrFail($id);

        $summary = $this->aggregationService->getOrderSummary($batch);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Resolve work orders from request filters.
     */
    protected function resolveWorkOrders(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        if ($request->has('batch_id')) {
            return $this->workOrderRepository->findByBatch(
                (int) $request->input('batch_id')
            );
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            return $this->workOrderRepository->findByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            );
        }

        if ($request->has('date')) {
            return $this->workOrderRepository->findByDate(
                $request->input('date')
            );
        }

        return $this->workOrderRepository->all();
    }
}
