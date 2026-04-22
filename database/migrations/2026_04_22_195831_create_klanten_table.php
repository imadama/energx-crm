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
        Schema::create('klanten', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->string('email');
            $table->string('telefoon')->nullable();
            $table->string('straat')->nullable();
            $table->string('huisnummer')->nullable();
            $table->string('postcode')->nullable();
            $table->string('stad')->nullable();
            $table->text('notities')->nullable();
            $table->enum('bron', ['website', 'telefoon', 'email', 'doorverwijzing', 'anders'])->default('website');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klanten');
    }
};
