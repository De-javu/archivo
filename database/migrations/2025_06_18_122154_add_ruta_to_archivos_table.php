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
        Schema::table('archivos', function (Blueprint $table) {
            $table->string('ruta')->after('nombre'); // Agrega una columna 'ruta' de tipo string después de 'nombre'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            //
        });
    }
};
