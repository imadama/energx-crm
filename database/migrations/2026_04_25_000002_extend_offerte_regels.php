<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offerte_regels', function (Blueprint $table) {
            $table->string('type', 30)->default('product')->after('volgorde');
            $table->boolean('optioneel')->default(false)->after('type');
            $table->string('eenheid', 30)->nullable()->after('aantal');
            $table->tinyInteger('btw_tarief')->default(21)->after('eenheidsprijs');
            $table->decimal('btw_bedrag', 10, 2)->default(0)->after('btw_tarief');
        });
    }

    public function down(): void
    {
        Schema::table('offerte_regels', function (Blueprint $table) {
            $table->dropColumn(['type', 'optioneel', 'eenheid', 'btw_tarief', 'btw_bedrag']);
        });
    }
};
