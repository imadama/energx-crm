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
        Schema::create('ticket_reacties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->enum('type', ['klant', 'intern', 'notitie'])->default('klant');
            $table->foreignId('gebruiker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('bron', ['email', 'whatsapp', 'telefoon', 'portaal'])->default('email');
            $table->text('inhoud');
            $table->json('bijlagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_reacties');
    }
};
