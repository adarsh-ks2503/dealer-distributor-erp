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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->string('code');
            $table->string('mobile_no');
            $table->string('email')->nullable();
            $table->string('gst_num')->nullable();
            $table->string('pan_num')->nullable();
            $table->integer('order_limit');
            $table->integer('allowed_order_limit');
            $table->longText('remarks')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Rejected'])->default('Pending');
            $table->enum('type', ['Wholesale', 'Retail']);
            $table->string('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('account_number')->nullable();
            $table->dateTime('approval_time')->nullable();
            $table->string('created_by');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
