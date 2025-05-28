<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Livewire\Volt\on;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carpetas', function (Blueprint $table) {
            $table->id(); // Se define una columna 'id'
            $table->string('nombre'); // se define una columna 'nombre' de tipo string

            $table->unsignedBigInteger('carpeta_padre_id') // Se define una columna 'carpeta_padre_id' de tipo unsignedBigInteger para gran tamaño
            ->nullable();// Permte que una carpeta no tenga carpeta padre, dando felxibilidad a la estructura jerárquica
        
            
            $table->foreign('carpeta_padre_id')// Define una columna 'carpeta_padre_id' sera una clave foránea
            ->references('id')// Hace referencia a la columna 'id' de la tabla
            ->on('carpetas')// Le idica a la base  de datos que  la relacion es con la tabla 'carpetas'
            ->onUpdate('cascade') // Si se actualiza una carpeta, se actualizarán también sus subcarpetas
            ->onDelete('cascade'); // Si se elimina una carpeta, se eliminarán también sus subcarpetas
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpetas');
    }
};
