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
        Schema::create('producten', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->text('beschrijving')->nullable();
            $table->decimal('prijs', 10, 2);
            $table->enum('categorie', ['laadpaal', 'installatie', 'thuisbatterij', 'warmtepomp', 'accessoire', 'overig'])->default('overig');
            $table->string('merk')->nullable();
            $table->boolean('actief')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producten');
    }
};
