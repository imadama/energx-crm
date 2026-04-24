<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offertes', function (Blueprint $table) {
            $table->boolean('geaccepteerd_akkoord')->default(false)->after('geaccepteerd_door');
            $table->string('geaccepteerd_handtekening_type')->nullable()->after('geaccepteerd_akkoord'); // typed|drawn
            $table->longText('geaccepteerd_handtekening')->nullable()->after('geaccepteerd_handtekening_type'); // typed text or dataURL (png)
        });
    }

    public function down(): void
    {
        Schema::table('offertes', function (Blueprint $table) {
            $table->dropColumn([
                'geaccepteerd_handtekening',
                'geaccepteerd_handtekening_type',
                'geaccepteerd_akkoord',
            ]);
        });
    }
};

