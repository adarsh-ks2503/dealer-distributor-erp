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
        Schema::create('item_bundles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('bundle_name');
            $table->unsignedBigInteger('size_id');
            $table->integer('pieces');
            $table->integer('initial_range');
            $table->integer('end_range');
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->longText('remarks')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('item_sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_bundles');
    }
};
