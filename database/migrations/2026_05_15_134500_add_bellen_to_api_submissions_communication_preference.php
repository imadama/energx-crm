<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE api_submissions MODIFY COLUMN communication_preference ENUM('email','whatsapp','bellen') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE api_submissions MODIFY COLUMN communication_preference ENUM('email','whatsapp') NULL");
    }
};
