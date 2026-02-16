<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'uom' => 'nullable|string|in:piece,kg,meter,liter,sqm,box',
            'lead_time_days' => 'nullable|integer|min:0',
            'waste_rate' => 'nullable|numeric|min:0|max:100',
            'unit_weight' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'specifications.color' => 'nullable|string',
            'specifications.size' => 'nullable|string',
            'specifications.material' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product->load('category'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'workOrders'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $id,
            'uom' => 'nullable|string|in:piece,kg,meter,liter,sqm,box',
            'lead_time_days' => 'nullable|integer|min:0',
            'waste_rate' => 'nullable|numeric|min:0|max:100',
            'unit_weight' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'specifications.color' => 'nullable|string',
            'specifications.size' => 'nullable|string',
            'specifications.material' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product->fresh('category'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        if ($product->workOrders()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with existing work orders.',
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
