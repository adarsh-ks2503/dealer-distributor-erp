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
        Schema::create('item_sizes_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("item");
            $table->integer("size");
            $table->integer("rate");
            $table->string("hsn_code")->nullable();
            $table->longText('remarks')->nullable();
            $table->enum('status', ['Rejected', 'Approved']);
            $table->dateTime('approval_time');
            $table->string('approved_by');
            $table->timestamps();

            // Composite unique constraint: item + size must be unique
            // $table->unique(['item', 'size','status']);

            // Foreign key constraint
            $table->foreign('item')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sizes_histories');
    }
};
