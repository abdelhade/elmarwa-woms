<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use App\Services\OrderProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class WorkOrderController extends Controller
{
    public function __construct(
        protected WorkOrderRepositoryInterface $workOrderRepository,
        protected OrderProcessingService $orderProcessingService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('status')) {
            $status = WorkOrderStatus::tryFrom($request->input('status'));
            if ($status !== null) {
                $workOrders = $this->workOrderRepository->findByStatus($status);

                return response()->json([
                    'success' => true,
                    'data' => $workOrders,
                ]);
            }
        }

        if ($request->has('client_id')) {
            $workOrders = $this->workOrderRepository->findByClient(
                (int) $request->input('client_id')
            );

            return response()->json([
                'success' => true,
                'data' => $workOrders,
            ]);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $workOrders = $this->workOrderRepository->findByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            );

            return response()->json([
                'success' => true,
                'data' => $workOrders,
            ]);
        }

        $workOrders = $this->workOrderRepository->all();

        return response()->json([
            'success' => true,
            'data' => $workOrders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'product_id' => 'required|integer|exists:products,id',
            'order_batch_id' => 'nullable|integer|exists:order_batches,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $workOrder = $this->orderProcessingService->createWorkOrder($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work order created successfully.',
            'data' => $workOrder,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $workOrder = $this->workOrderRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $workOrder,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $workOrder = $this->workOrderRepository->findOrFail($id);

        $validated = $request->validate([
            'client_id' => 'sometimes|required|integer|exists:clients,id',
            'product_id' => 'sometimes|required|integer|exists:products,id',
            'order_batch_id' => 'nullable|integer|exists:order_batches,id',
            'quantity' => 'sometimes|required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $workOrder = $this->orderProcessingService->updateWorkOrder($workOrder, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Work order updated successfully.',
            'data' => $workOrder,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $workOrder = $this->workOrderRepository->findOrFail($id);

        $this->workOrderRepository->delete($workOrder);

        return response()->json([
            'success' => true,
            'message' => 'Work order deleted successfully.',
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $workOrder = $this->workOrderRepository->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ]);

        $newStatus = WorkOrderStatus::from($validated['status']);

        try {
            $workOrder = $this->orderProcessingService->updateStatus($workOrder, $newStatus);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Work order status updated successfully.',
            'data' => $workOrder,
        ]);
    }
}
