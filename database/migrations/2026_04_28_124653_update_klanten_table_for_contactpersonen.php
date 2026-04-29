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
        Schema::table('klanten', function (Blueprint $table) {
            $table->enum('soort', ['bedrijf', 'particulier'])->default('particulier')->after('id');
            $table->string('naam')->nullable()->change();
            $table->dropColumn(['email', 'telefoon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klanten', function (Blueprint $table) {
            $table->string('email')->after('naam');
            $table->string('telefoon')->nullable()->after('email');
            $table->string('naam')->nullable(false)->change();
            $table->dropColumn('soort');
        });
    }
};
