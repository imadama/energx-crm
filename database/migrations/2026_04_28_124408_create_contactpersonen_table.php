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
        Schema::create('contactpersonen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klant_id')->constrained('klanten')->cascadeOnDelete();
            $table->string('voornaam');
            $table->string('achternaam');
            $table->string('email');
            $table->string('telefoon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactpersonen');
    }
};
