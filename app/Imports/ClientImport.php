<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Client([
            'name'         => $row['name'],
            'email'        => $row['email'] ?? null,
            'phone'        => $row['phone'] ?? null,
            'address'      => $row['address'] ?? null,
            'company'      => $row['company'] ?? null,
            'tax_number'   => $row['tax_number'] ?? null,
            'credit_limit' => $row['credit_limit'] ?? 0,
            'is_active'    => $row['is_active'] ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:30',
            'credit_limit' => 'nullable|numeric',
        ];
    }
}
