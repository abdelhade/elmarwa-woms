<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_batches', function (Blueprint $table): void {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('type', 20)->default('project');
            $table->string('name');
            $table->date('date')->nullable();
            $table->string('shift', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_batches');
    }
};
