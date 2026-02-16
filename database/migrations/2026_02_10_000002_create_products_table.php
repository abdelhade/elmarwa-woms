<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('restrict');
            $table->string('name')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->string('uom', 20)->nullable()->default('piece');
            $table->unsignedInteger('lead_time_days')->default(0);
            $table->decimal('waste_rate', 5, 2)->nullable()->default(0.00);
            $table->decimal('unit_weight', 10, 4)->nullable()->default(0.0000);
            $table->decimal('unit_price', 12, 2)->nullable()->default(0.00);
            $table->json('specifications')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('uom');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
