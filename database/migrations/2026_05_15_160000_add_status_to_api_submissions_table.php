<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_submissions', function (Blueprint $table) {
            $table->enum('status', ['nieuw', 'in_behandeling', 'offerte_gemaakt', 'afgerond', 'afgewezen'])
                  ->default('nieuw')
                  ->after('offerte_id');
            $table->text('notitie')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('api_submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'notitie']);
        });
    }
};
