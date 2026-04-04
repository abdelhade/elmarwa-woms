<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Models\Client;
use App\Models\Product;
use App\Models\OrderBatch;
use App\Models\WorkOrder;
use App\Models\Attribute;
use App\Models\Employee;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Services\OrderProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class WorkOrderController extends Controller
{
    public function __construct(
        private readonly OrderProcessingService $orderProcessingService,
    ) {}

    public function index(Request $request): View
    {
        $query = WorkOrder::with(['client', 'product', 'orderBatch', 'details.product', 'employee', 'operator', 'supervisor']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $workOrders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $clients = Client::orderBy('sort_order')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $statuses = WorkOrderStatus::cases();

        return view('work-orders.index', compact('workOrders', 'clients', 'products', 'statuses'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('sort_order')->orderBy('name')->get();
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $batches = OrderBatch::orderByDesc('created_at')->get();
        $allAttributes = Attribute::all();
        $allCategoryAttributes = CategoryAttribute::all();
        $categories = Category::orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        return view('work-orders.create', compact('clients', 'products', 'batches', 'allAttributes', 'allCategoryAttributes', 'categories', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'nullable|date|after_or_equal:today',
            'order_batch_id' => 'nullable|exists:order_batches,id',
            'employee_id' => 'nullable|exists:employees,id',
            'operator_id' => 'nullable|exists:employees,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|min:0.01',
            'details.*.category_attributes' => 'nullable|array',
            'details.*.attributes' => 'nullable|array',
        ]);
        
        foreach ($data['details'] as &$detail) {
            $detail['unit_price'] = 0;
        }

        $this->orderProcessingService->createWorkOrderWithDetails($data);

        return redirect()->route('work-orders.create')
            ->with('success', 'تم إنشاء أمر العمل بنجاح.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['client', 'product.category', 'orderBatch', 'details.product.category', 'employee', 'operator', 'supervisor']);
        return view('work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder): View
    {
        $clients = Client::orderBy('sort_order')->orderBy('name')->get();
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $batches = OrderBatch::orderByDesc('created_at')->get();
        $allAttributes = Attribute::all();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $workOrder->load('details.product.category');
        return view('work-orders.edit', compact('workOrder', 'clients', 'products', 'batches', 'allAttributes', 'employees'));
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'order_batch_id' => 'nullable|exists:order_batches,id',
            'employee_id' => 'nullable|exists:employees,id',
            'operator_id' => 'nullable|exists:employees,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|min:0.01',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.attributes' => 'nullable|array',
        ]);

        $this->orderProcessingService->updateWorkOrderWithDetails($workOrder, $data);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'تم تحديث أمر العمل بنجاح.');
    }

    public function duplicate(WorkOrder $workOrder): RedirectResponse
    {
        $newOrder = $this->orderProcessingService->duplicateWorkOrder($workOrder);

        return redirect()->route('work-orders.edit', $newOrder)
            ->with('success', 'تم تكرار أمر العمل بنجاح. يمكنك الآن تعديله.');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'تم حذف أمر العمل بنجاح.');
    }

    public function printDetailed(WorkOrder $workOrder): View
    {
        $workOrder->load(['client', 'product.category', 'orderBatch', 'details.product.category', 'employee', 'operator', 'supervisor']);
        return view('work-orders.print-detailed', compact('workOrder'));
    }

    public function printSummary(WorkOrder $workOrder): View
    {
        $workOrder->load(['client', 'product.category', 'orderBatch', 'details.product.category', 'employee', 'operator', 'supervisor']);
        
        $summary = $workOrder->details->groupBy(function($detail) {
            return $detail->product->category->name ?? 'بدون تصنيف';
        })->map(function($items) {
            return $items->sum('quantity');
        });

        return view('work-orders.print-summary', compact('workOrder', 'summary'));
    }

    public function print(WorkOrder $workOrder): View
    {
        $workOrder->load(['client', 'product.category', 'orderBatch', 'details.product.category', 'employee', 'operator', 'supervisor']);
        return view('work-orders.print', compact('workOrder'));
    }

    /**
     * AJAX: Search products for the data grid.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->limit(50)->get();

        return response()->json($products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'unit_price' => (float) $p->unit_price,
                'category_name' => $p->category?->name ?? '—',
                'uom' => $p->uom?->value ?? '',
            ];
        }));
    }
}
