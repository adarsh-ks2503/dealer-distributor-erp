<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Remove AUTO_INCREMENT and make id nullable temporarily
        DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NULL');

        // Step 2: Drop the primary key
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary('id');
        });

        // Step 3: Change column to UUID
        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->primary('id');
        });

        // Step 4: Generate UUIDs for existing rows
        DB::statement("UPDATE notifications SET id = UUID() WHERE id IS NOT NULL");

        // Step 5: Make id NOT NULL
        DB::statement('ALTER TABLE notifications MODIFY id CHAR(36) NOT NULL');
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary('id');
            $table->bigIncrements('id')->change();
            $table->primary('id');
        });
    }
};
