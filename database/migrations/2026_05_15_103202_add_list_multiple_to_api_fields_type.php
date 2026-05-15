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
        // MySQL ENUM uitbreiden met list_multiple
        DB::statement("ALTER TABLE api_fields MODIFY COLUMN type ENUM('text','integer','decimal','list','list_multiple') NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE api_fields MODIFY COLUMN type ENUM('text','integer','decimal','list') NOT NULL DEFAULT 'text'");
    }
};
