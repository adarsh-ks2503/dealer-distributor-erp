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
        Schema::create('distributor_team_dealers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_team_id')
                  ->constrained('distributor_teams')
                  ->onDelete('cascade');
            $table->foreignId('dealer_id')
                  ->constrained('dealers')
                  ->onDelete('cascade');
            $table->enum('status', ['Active', 'Inactive','Suspended'])->default('Active');
            $table->timestamps();

            // Prevent duplicate dealer-team assignments
            $table->unique(['distributor_team_id', 'dealer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_team_dealers');
    }
};
