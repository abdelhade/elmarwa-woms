<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CategoryAttribute;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CategoryAttributeController extends Controller
{
    public function index(Request $request): View
    {
        $query = CategoryAttribute::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $attributes = $query->orderBy('sort_order')->orderByDesc('created_at')->paginate(15)->withQueryString();
        
        
        $types = [
            'text' => 'نصي (Text)',
            'number' => 'رقمي (Number)',
            'select' => 'قائمة (Select)',
        ];

        return view('category-attributes.index', compact('attributes', 'types'));
    }

    public function create(): View
    {
        $types = [
            'text' => 'نصي (Text)',
            'number' => 'رقمي (Number)',
            'select' => 'قائمة (Select)',
        ];
        return view('category-attributes.create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,select',
            'is_required' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        $data['is_required'] = $request->has('is_required');

        if ($data['type'] !== 'select') {
            $data['options'] = null;
        }

        CategoryAttribute::create($data);

        return redirect()->route('category-attributes.create')
            ->with('success', 'تم إنشاء خاصية التصنيف بنجاح.');
    }

    public function edit(CategoryAttribute $categoryAttribute): View
    {
        $types = [
            'text' => 'نصي (Text)',
            'number' => 'رقمي (Number)',
            'select' => 'قائمة (Select)',
        ];
        return view('category-attributes.edit', compact('categoryAttribute', 'types'));
    }

    public function update(Request $request, CategoryAttribute $categoryAttribute): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,select',
            'is_required' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        $data['is_required'] = $request->has('is_required');

        if ($data['type'] !== 'select') {
            $data['options'] = null;
        }

        $categoryAttribute->update($data);

        return redirect()->route('category-attributes.index')
            ->with('success', 'تم تحديث خاصية التصنيف بنجاح.');
    }

    public function destroy(CategoryAttribute $categoryAttribute): RedirectResponse
    {
        $categoryAttribute->delete();

        return redirect()->route('category-attributes.index')
            ->with('success', 'تم حذف خاصية التصنيف بنجاح.');
    }
}
