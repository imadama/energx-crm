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
        Schema::create('offertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klant_id')->constrained('klanten')->cascadeOnDelete();
            $table->string('nummer')->unique(); // ENX-2026-0001
            $table->string('token', 64)->unique(); // publieke viewer-link token
            $table->enum('status', ['concept', 'verstuurd', 'bekeken', 'geaccepteerd', 'afgewezen', 'verlopen'])->default('concept');
            $table->text('inleiding')->nullable();
            $table->decimal('subtotaal', 10, 2)->default(0);
            $table->decimal('btw_bedrag', 10, 2)->default(0);
            $table->decimal('totaal', 10, 2)->default(0);
            $table->date('geldig_tot')->nullable();
            $table->timestamp('verstuurd_op')->nullable();
            $table->timestamp('bekeken_op')->nullable();
            $table->timestamp('geaccepteerd_op')->nullable();
            $table->string('geaccepteerd_door')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offertes');
    }
};
