<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClientImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    public function model(array $row): ?Client
    {
        $name = trim($row['name'] ?? '');
        if ($name === '') {
            return null;
        }

        return new Client([
            'name'         => $name,
            'email'        => isset($row['email']) && $row['email'] !== '' ? $row['email'] : null,
            'phone'        => isset($row['phone']) && $row['phone'] !== '' ? $row['phone'] : null,
            'address'      => isset($row['address']) && $row['address'] !== '' ? $row['address'] : null,
            'company'      => isset($row['company']) && $row['company'] !== '' ? $row['company'] : null,
            'tax_number'   => isset($row['tax_number']) && $row['tax_number'] !== '' ? $row['tax_number'] : null,
            'credit_limit' => is_numeric($row['credit_limit'] ?? null) ? $row['credit_limit'] : 0,
            'is_active'    => true,
        ]);
    }

    // Insert 500 rows per batch (single multi-row INSERT)
    public function batchSize(): int
    {
        return 500;
    }

    // Read 500 rows from Excel at a time (keeps memory low)
    public function chunkSize(): int
    {
        return 500;
    }
}
