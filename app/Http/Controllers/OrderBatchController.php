<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BatchType;
use App\Models\OrderBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class OrderBatchController extends Controller
{
    public function index(Request $request): View
    {
        $query = OrderBatch::withCount('workOrders');

        if ($request->filled('search')) {
            $query->where('batch_number', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $batches = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $types = BatchType::cases();

        return view('order-batches.index', compact('batches', 'types'));
    }

    public function create(): View
    {
        $types = BatchType::cases();
        return view('order-batches.create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'batch_number' => 'required|string|max:100|unique:order_batches,batch_number',
            'type' => 'required|string',
            'date' => 'required|date',
            'shift' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        OrderBatch::create($data);

        return redirect()->route('order-batches.create')
            ->with('success', 'تم إنشاء الدفعة بنجاح.');
    }

    public function show(OrderBatch $orderBatch): View
    {
        $orderBatch->load(['workOrders.client', 'workOrders.product']);
        return view('order-batches.show', compact('orderBatch'));
    }

    public function edit(OrderBatch $orderBatch): View
    {
        $types = BatchType::cases();
        return view('order-batches.edit', compact('orderBatch', 'types'));
    }

    public function update(Request $request, OrderBatch $orderBatch): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'batch_number' => 'required|string|max:100|unique:order_batches,batch_number,' . $orderBatch->id,
            'type' => 'required|string',
            'date' => 'required|date',
            'shift' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $orderBatch->update($data);

        return redirect()->route('order-batches.show', $orderBatch)
            ->with('success', 'تم تحديث الدفعة بنجاح.');
    }

    public function destroy(OrderBatch $orderBatch): RedirectResponse
    {
        if ($orderBatch->workOrders()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف دفعة تحتوي على أوامر عمل.');
        }

        $orderBatch->delete();

        return redirect()->route('order-batches.index')
            ->with('success', 'تم حذف الدفعة بنجاح.');
    }

    public function print(OrderBatch $orderBatch): View
    {
        $orderBatch->load(['workOrders.client', 'workOrders.product']);
        return view('order-batches.print', compact('orderBatch'));
    }
}
