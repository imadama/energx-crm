<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerte_secties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerte_id')->constrained('offertes')->cascadeOnDelete();
            $table->enum('type', ['voorblad', 'introductie', 'prijzen', 'product', 'werkwijze', 'acceptatie', 'tekst']);
            $table->string('titel');
            $table->json('inhoud')->nullable();
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerte_secties');
    }
};
