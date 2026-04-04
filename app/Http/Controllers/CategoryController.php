<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::with('parent')->withCount('children', 'products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('categories.index', compact('categories', 'parents'));
    }

    public function create(): View
    {
        $parents = Category::orderBy('name')->get();
        return view('categories.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string|max:255',
            'attributes.*.type' => 'required|string|in:text,number,select',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $category = Category::create($data);

        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $index => $attr) {
                $category->attributes()->create([
                    'name' => $attr['name'],
                    'type' => $attr['type'],
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('categories.create')
            ->with('success', 'تم إنشاء التصنيف بنجاح.');
    }

    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'products']);
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $parents = Category::where('id', '!=', $category->id)->orderBy('name')->get();
        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string|max:255',
            'attributes.*.type' => 'required|string|in:text,number,select',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        // تحديث الخصائص (بسيط: حذف القديم وإضافة الجديد)
        $category->attributes()->delete();
        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $index => $attr) {
                $category->attributes()->create([
                    'name' => $attr['name'],
                    'type' => $attr['type'],
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('categories.show', $category)
            ->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->count() > 0 || $category->products()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف تصنيف يحتوي على تصنيفات فرعية أو منتجات.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new CategoryImport, $request->file('file'));
            return back()->with('success', 'تم استيراد الفئات بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        $headers = ['name', 'parent_id', 'description'];
        $data = [
            ['Electronics', '', 'Category for electronic items'],
            ['Laptops', '1', 'Subcategory for laptops'],
        ];

        return response()->streamDownload(function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'categories_template.csv');
    }

    public function getAttributes(Category $category)
    {
        return response()->json($category->attributes);
    }
}
