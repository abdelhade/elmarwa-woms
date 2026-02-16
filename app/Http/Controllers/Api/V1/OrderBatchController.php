<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\BatchType;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderBatchRepositoryInterface;
use App\Services\OrderProcessingService;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderBatchController extends Controller
{
    public function __construct(
        protected OrderBatchRepositoryInterface $orderBatchRepository,
        protected WorkOrderRepositoryInterface $workOrderRepository,
        protected OrderProcessingService $orderProcessingService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('type')) {
            $type = BatchType::tryFrom($request->input('type'));
            if ($type !== null) {
                $batches = $this->orderBatchRepository->findByType($type);

                return response()->json([
                    'success' => true,
                    'data' => $batches,
                ]);
            }
        }

        if ($request->has('date')) {
            $batches = $this->orderBatchRepository->findByDate(
                $request->input('date')
            );

            return response()->json([
                'success' => true,
                'data' => $batches,
            ]);
        }

        $batches = $this->orderBatchRepository->all();

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:project,daily_shift',
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'shift' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $batch = $this->orderProcessingService->createOrderBatch($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order batch created successfully.',
            'data' => $batch,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $batch = $this->orderBatchRepository->findWithWorkOrders($id);

        if ($batch === null) {
            return response()->json([
                'success' => false,
                'message' => 'Order batch not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $batch = $this->orderBatchRepository->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|required|string|in:project,daily_shift',
            'name' => 'sometimes|required|string|max:255',
            'date' => 'nullable|date',
            'shift' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $batch = $this->orderBatchRepository->update($batch, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Order batch updated successfully.',
            'data' => $batch,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $batch = $this->orderBatchRepository->findOrFail($id);

        if ($batch->workOrders()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete batch with assigned work orders. Remove or reassign them first.',
            ], 422);
        }

        $this->orderBatchRepository->delete($batch);

        return response()->json([
            'success' => true,
            'message' => 'Order batch deleted successfully.',
        ]);
    }

    public function addWorkOrder(Request $request, int $id): JsonResponse
    {
        $batch = $this->orderBatchRepository->findOrFail($id);

        $validated = $request->validate([
            'work_order_id' => 'required|integer|exists:work_orders,id',
        ]);

        $workOrder = $this->workOrderRepository->findOrFail((int) $validated['work_order_id']);

        $workOrder = $this->orderProcessingService->assignToBatch($workOrder, $batch);

        return response()->json([
            'success' => true,
            'message' => 'Work order assigned to batch successfully.',
            'data' => $workOrder,
        ]);
    }
}
