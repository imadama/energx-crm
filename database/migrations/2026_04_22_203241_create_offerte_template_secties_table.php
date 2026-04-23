<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerte_template_secties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('offerte_templates')->cascadeOnDelete();
            $table->enum('type', ['voorblad', 'introductie', 'prijzen', 'product', 'werkwijze', 'acceptatie', 'tekst']);
            $table->string('titel');
            $table->json('inhoud')->nullable(); // type-specifieke content als JSON
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerte_template_secties');
    }
};
