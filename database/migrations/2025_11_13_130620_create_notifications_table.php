<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // Polymorphic: allows linking to users or app_users (stores notifiable_id and notifiable_type)
            $table->string('type'); // e.g., 'new_dealer_added', 'order_placed', 'order_approved'
            $table->text('data'); // JSON data for details, e.g., {'order_id': 123, 'message': 'Order approved'}
            $table->timestamp('read_at')->nullable(); // When the user read it
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
