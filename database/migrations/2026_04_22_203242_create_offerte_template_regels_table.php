<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('offerte_template_regels');
    }
};
