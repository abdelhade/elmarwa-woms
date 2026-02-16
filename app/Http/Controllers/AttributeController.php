<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class AttributeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attribute::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $attributes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $types = Attribute::types();

        return view('attributes.index', compact('attributes', 'types'));
    }

    public function create(): View
    {
        $types = Attribute::types();
        return view('attributes.create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select',
            'options' => 'nullable|array',
            'options.*' => 'required|string|max:255',
            'is_required' => 'boolean',
        ]);

        $data['is_required'] = $request->has('is_required');

        Attribute::create($data);

        return redirect()->route('attributes.create')
            ->with('success', 'تم إنشاء الخاصية بنجاح.');
    }

    public function edit(Attribute $attribute): View
    {
        $types = Attribute::types();
        return view('attributes.edit', compact('attribute', 'types'));
    }

    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select',
            'options' => 'nullable|array',
            'options.*' => 'required|string|max:255',
            'is_required' => 'boolean',
        ]);

        $data['is_required'] = $request->has('is_required');

        $attribute->update($data);

        return redirect()->route('attributes.index')
            ->with('success', 'تم تحديث الخاصية بنجاح.');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('attributes.index')
            ->with('success', 'تم حذف الخاصية بنجاح.');
    }
}
