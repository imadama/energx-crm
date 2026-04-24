<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offertes', function (Blueprint $table) {
            $table->json('document')->nullable()->after('inleiding');
            $table->foreignId('template_id')->nullable()->change();
        });

        Schema::table('offerte_templates', function (Blueprint $table) {
            $table->json('document')->nullable()->after('beschrijving');
        });
    }

    public function down(): void
    {
        Schema::table('offertes', function (Blueprint $table) {
            $table->dropColumn('document');
        });
        Schema::table('offerte_templates', function (Blueprint $table) {
            $table->dropColumn('document');
        });
    }
};
