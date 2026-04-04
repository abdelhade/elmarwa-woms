<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UnitOfMeasure;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('uom')) {
            $query->where('uom', $request->uom);
        }

        $products = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $units = UnitOfMeasure::cases();

        return view('products.index', compact('products', 'categories', 'units'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = UnitOfMeasure::cases();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'uom' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'unit_weight' => 'nullable|numeric|min:0',
            'waste_rate' => 'nullable|numeric|min:0|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'specifications.color' => 'nullable|string',
            'specifications.size' => 'nullable|string',
            'specifications.material' => 'nullable|string',
        ]);

        $data['unit_price'] = $data['unit_price'] ?? 0;
        $data['unit_weight'] = $data['unit_weight'] ?? 0;
        $data['waste_rate'] = $data['waste_rate'] ?? 0;
        $data['lead_time_days'] = $data['lead_time_days'] ?? 0;

        $product = Product::create($data);

        // Auto-generate SKU from ID if not provided
        if (empty($product->sku)) {
            $product->update(['sku' => (string) $product->id]);
        }

        return redirect()->route('products.create')
            ->with('success', 'تم إنشاء المنتج بنجاح.');
    }

    public function show(Product $product): View
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $units = UnitOfMeasure::cases();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'uom' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'unit_weight' => 'nullable|numeric|min:0',
            'waste_rate' => 'nullable|numeric|min:0|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'specifications.color' => 'nullable|string',
            'specifications.size' => 'nullable|string',
            'specifications.material' => 'nullable|string',
        ]);

        $data['unit_price'] = $data['unit_price'] ?? 0;
        $data['unit_weight'] = $data['unit_weight'] ?? 0;
        $data['waste_rate'] = $data['waste_rate'] ?? 0;
        $data['lead_time_days'] = $data['lead_time_days'] ?? 0;

        if (empty($data['sku'])) {
            $data['sku'] = (string) $product->id;
        }

        $product->update($data);

        return redirect()->route('products.show', $product)
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->workOrders()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف منتج مرتبط بأوامر عمل.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));
            return back()->with('success', 'تم استيراد المنتجات بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        $headers = ['category_id', 'name', 'sku', 'uom', 'lead_time_days', 'waste_rate', 'unit_weight', 'unit_price', 'description'];
        $data = [
            ['1', 'Sample Product', 'SKU-001', 'piece', '5', '0.05', '0.5', '100', 'Product description here'],
        ];

        return response()->streamDownload(function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'products_template.csv');
    }
}
