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
        Schema::create('order_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            $table->enum('allocated_to_type', ['dealer', 'distributor']);
            $table->unsignedBigInteger('allocated_to_id'); // dealer_id or distributor_id

            $table->index(['allocated_to_id', 'allocated_to_type']);
            
            $table->float('qty');
            $table->double('basic_price');
            $table->double('agreed_basic_price');
            $table->double('token_amount')->nullable();

            $table->float('dispatched_qty')->default(0);
            $table->float('remaining_qty')->default(0);

            $table->string('payment_terms');
            $table->string('status')->default('pending');
            $table->longText('remarks')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_allocations');
    }
};
