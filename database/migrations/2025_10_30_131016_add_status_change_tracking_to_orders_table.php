<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status_changed_by')->nullable()->after('status');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
            $table->text('status_change_remarks')->nullable()->after('status_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status_changed_by', 'status_changed_at', 'status_change_remarks']);
        });
    }
};
