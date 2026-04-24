<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('template_identifier');
            $table->enum('communication_preference', ['email', 'whatsapp'])->nullable();
            $table->string('customer_email')->nullable();
            $table->json('payload');
            $table->json('details')->nullable();
            $table->foreignId('offerte_id')->nullable()->constrained('offertes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_submissions');
    }
};

