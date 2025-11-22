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
        Schema::create('item_basic_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item');
            $table->unsignedBigInteger('region');
            $table->integer('market_basic_price');
            $table->integer('distributor_basic_price');
            $table->integer('dealer_basic_price');
            $table->longText('remarks')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Old'])->default('Pending');
            $table->dateTime('approval_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('item')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('region')->references('id')->on('states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_basic_prices');
    }
};
