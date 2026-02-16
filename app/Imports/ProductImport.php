<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Product([
            'category_id'    => $row['category_id'],
            'name'           => $row['name'],
            'sku'            => $row['sku'] ?? null,
            'uom'            => $row['uom'] ?? 'piece',
            'lead_time_days' => $row['lead_time_days'] ?? 0,
            'waste_rate'     => $row['waste_rate'] ?? 0,
            'unit_weight'    => $row['unit_weight'] ?? 0,
            'unit_price'     => $row['unit_price'] ?? 0,
            'description'    => $row['description'] ?? null,
            'is_active'      => $row['is_active'] ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'unit_price' => 'nullable|numeric',
            'unit_weight' => 'nullable|numeric',
        ];
    }
}
