<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');
            $table->foreignId('order_batch_id')
                ->nullable()
                ->constrained('order_batches')
                ->onDelete('set null');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 14, 2)->default(0.00);
            $table->string('status', 20)->default('pending');
            $table->json('specifications')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('product_id');
            $table->index('order_batch_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
