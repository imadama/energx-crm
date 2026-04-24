<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producten', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->index()->after('actief');
            $table->string('generator_mode')->default('manual')->after('order'); // manual|always|conditional
            $table->json('generator_conditions')->nullable()->after('generator_mode');
            $table->json('generator_value_rules')->nullable()->after('generator_conditions');
        });
    }

    public function down(): void
    {
        Schema::table('producten', function (Blueprint $table) {
            $table->dropColumn([
                'generator_value_rules',
                'generator_conditions',
                'generator_mode',
                'order',
            ]);
        });
    }
};

