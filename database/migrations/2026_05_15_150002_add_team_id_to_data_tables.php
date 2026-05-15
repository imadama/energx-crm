<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['klanten', 'producten', 'offertes', 'offerte_templates', 'api_submissions', 'tickets'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable()->after('id');
                $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['klanten', 'producten', 'offertes', 'offerte_templates', 'api_submissions', 'tickets'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            });
        }
    }
};
