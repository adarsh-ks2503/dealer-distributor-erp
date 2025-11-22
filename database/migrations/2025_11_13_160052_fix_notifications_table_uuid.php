<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Drop existing primary key and auto-increment
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary('id');
        });

        // Step 2: Change id to UUID with default
        DB::statement('ALTER TABLE notifications MODIFY id CHAR(36) NOT NULL DEFAULT (UUID())');

        // Step 3: Re-add primary key
        Schema::table('notifications', function (Blueprint $table) {
            $table->primary('id');
        });

        // Step 4: Update existing NULL ids
        DB::statement("UPDATE notifications SET id = UUID() WHERE id IS NULL OR id = ''");
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary('id');
            $table->bigIncrements('id')->first();
            $table->primary('id');
        });
    }
};
