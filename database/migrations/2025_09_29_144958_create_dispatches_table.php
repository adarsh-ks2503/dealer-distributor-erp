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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number')->unique(); // Auto-generated, e.g., 'DISP-2025-09-29-001'
            $table->string('type'); // 'distributor' or 'dealer'
            $table->foreignId('distributor_id')->nullable()->constrained('distributors')->onDelete('cascade'); // Assuming parties table exists; nullable if type='dealer'
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->onDelete('cascade'); // Nullable if type='distributor'

            // Billing Address
            $table->string('recipient_name');
            $table->text('recipient_address');
            $table->foreignId('recipient_state_id')->constrained('states')->onDelete('restrict');
            $table->foreignId('recipient_city_id')->constrained('cities')->onDelete('restrict');
            $table->string('recipient_pincode');

            // Delivery Address
            $table->string('consignee_name');
            $table->text('consignee_address');
            $table->foreignId('consignee_state_id')->constrained('states')->onDelete('restrict');
            $table->foreignId('consignee_city_id')->constrained('cities')->onDelete('restrict');
            $table->string('consignee_pincode');
            $table->string('consignee_mobile_no')->nullable();

            // Dispatch Details
            $table->date('dispatch_date');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->string('bill_to')->nullable();
            $table->string('bill_number')->nullable();
            $table->time('dispatch_out_time')->nullable();
            $table->string('payment_slip')->nullable(); // File path, e.g., 'uploads/dispatches/payment_slips/abc.jpg'
            $table->text('dispatch_remarks')->nullable();

            // Transport & Vehicle Details
            $table->string('transporter_name')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_mobile_no')->nullable();
            $table->string('e_way_bill_no')->nullable();
            $table->string('bilty_no')->nullable();
            $table->text('transport_remarks')->nullable();

            // Terms & Conditions
            $table->text('terms_conditions')->nullable();

            // Order Summary
            $table->decimal('additional_charges', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00); // Calculated and stored for quick access

            $table->string('status');
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
