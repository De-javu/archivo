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
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Define una columna 'nombre' de tipo string
            
            $table->unsignedBigInteger('carpeta_id') // Define una columna 'carpeta_id' de tipo unsignedBigInteger para gran tamaño
                  ->nullable(); // Permite que un archivo no tenga carpeta, dando flexibilidad a la estructura jerárquica
            
            $table->foreign('carpeta_id') // Define una columna 'carpeta_id' como clave foránea
                  ->references('id') // Hace referencia a la columna 'id' de la tabla 'carpetas'
                  ->on('carpetas') // Indica que la relación es con la tabla 'carpetas'
                  ->onUpdate('cascade') // Si se actualiza una carpeta, se actualizarán también sus archivos
                  ->onDelete('cascade'); // Si se elimina una carpeta, se eliminarán también sus archivos
            
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
