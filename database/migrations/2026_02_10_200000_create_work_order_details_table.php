<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_id')
                ->constrained('work_orders')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 14, 2)->default(0.00);
            $table->timestamps();

            $table->index('work_order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_details');
    }
};
