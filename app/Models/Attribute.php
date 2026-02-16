<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'options',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    /**
     * Get available types.
     */
    public static function types(): array
    {
        return [
            'text' => 'نصي (Text)',
            'number' => 'رقمي (Number)',
            'select' => 'قائمة (Select)',
        ];
    }
}
