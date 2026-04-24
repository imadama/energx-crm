<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('offerte_secties');
        Schema::dropIfExists('offerte_template_secties');
        Schema::dropIfExists('offerte_template_regels');
    }

    public function down(): void
    {
        Schema::create('offerte_secties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerte_id')->constrained('offertes')->cascadeOnDelete();
            $table->string('type');
            $table->string('titel');
            $table->json('inhoud')->nullable();
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });

        Schema::create('offerte_template_secties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('offerte_templates')->cascadeOnDelete();
            $table->string('type');
            $table->string('titel');
            $table->json('inhoud')->nullable();
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });

        Schema::create('offerte_template_regels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('offerte_templates')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('producten')->nullOnDelete();
            $table->string('naam');
            $table->text('beschrijving')->nullable();
            $table->integer('aantal')->default(1);
            $table->decimal('eenheidsprijs', 10, 2);
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });
    }
};
