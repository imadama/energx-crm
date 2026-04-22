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
        Schema::create('offerte_regels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerte_id')->constrained('offertes')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('producten')->nullOnDelete();
            $table->string('naam'); // snapshot van productnaam op moment van offerte
            $table->text('beschrijving')->nullable();
            $table->integer('aantal')->default(1);
            $table->decimal('eenheidsprijs', 10, 2);
            $table->decimal('totaal', 10, 2);
            $table->integer('volgorde')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerte_regels');
    }
};
