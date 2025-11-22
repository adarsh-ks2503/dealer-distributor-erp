<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('dispatches')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');         $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('allocation_id')->constrained('order_allocations')->onDelete('restrict');
            $table->decimal('order_qty', 10, 2);
            $table->decimal('already_disp', 10, 2);
            $table->decimal('remaining_qty', 10, 2);
            $table->string('item_name'); // e.g., 'TMT Bar'
            $table->foreignId('size_id')->constrained('item_sizes')->onDelete('restrict');
            $table->decimal('length', 10, 2)->nullable(); // In ft
            $table->decimal('dispatch_qty', 10, 2);
            $table->decimal('basic_price', 10, 2);
            $table->decimal('gauge_diff', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->decimal('loading_charge', 10, 2);
            $table->decimal('insurance', 10, 2);
            $table->decimal('gst', 5, 2); // Percentage, e.g., 18.00
            $table->decimal('token_amount', 10, 2)->nullable(); // Can be N/A in form, so nullable
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_term')->nullable(); // e.g., 'Advance'
            $table->string('status')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
    }
};
