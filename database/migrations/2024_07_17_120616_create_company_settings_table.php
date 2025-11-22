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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->string('gst_no', 100)->nullable();
            $table->string('pan', 100)->nullable();
            $table->string('tan', 100)->nullable();
            $table->integer('threshold')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('amount', 100)->nullable();
            $table->string('ac_number', 100)->nullable();
            $table->string('ifsc_code', 100)->nullable();
            $table->string('branch', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
