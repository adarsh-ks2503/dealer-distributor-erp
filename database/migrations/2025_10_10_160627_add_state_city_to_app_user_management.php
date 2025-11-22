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
        Schema::table('app_user_management', function (Blueprint $table) {
           $table->unsignedBigInteger('state_id')->nullable()->after('mobile_no');
           $table->unsignedBigInteger('city_id')->nullable()->after('state_id');

           // Add foreign key constraints if the states and cities tables exist
           $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
           $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
       });
   }

       /**
        * Reverse the migrations.
        */
    public function down(): void
    {
       Schema::table('app_user_management', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
           $table->dropForeign(['city_id']);
           $table->dropColumn(['state_id', 'city_id']);
       });
   }
};
