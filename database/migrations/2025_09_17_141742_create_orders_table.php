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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('type'); // 'dealer' or 'distributor' (who placed the order)
            $table->date('order_date');

            $table->foreignId('placed_by_dealer_id')->nullable()->constrained('dealers')->onDelete('cascade');
            $table->foreignId('placed_by_distributor_id')->nullable()->constrained('distributors')->onDelete('cascade');

            $table->string('payment_term')->nullable();
            $table->float('loading_charge');
            $table->float('insurance_charge');
            $table->float('token_amount')->nullable();

            $table->string('created_by');
            $table->string('status');
            $table->longText('remarks')->nullable();
            $table->longText('terms_conditions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
