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
        // Se crea la tabla para almacenar las columnas de la tabla carpetas
        Schema::create('carpetas', function (Blueprint $table) {

            // Se define la estructura de la tabla carpetas
            $table->id(); // Se define una columna 'id'
            $table->string('nombre'); // se define una columna 'nombre' de tipo string
            $table->unsignedBigInteger('user_id');// Define una columna de tipo string, que permite nombre de gran tamaño
        
            // Se crea la columna que Permite que una carpeta pueda tener una carpeta padre, creando una estructura de árbol
            $table->unsignedBigInteger('carpeta_padre_id')  // Se define una columna 'carpeta_padre_id' de tipo unsignedBigInteger para gran tamaño
                  ->nullable();// Permte que una carpeta no tenga carpeta padre, dando felxibilidad a la estructura jerárquica llena la columna con null si no tiene carpeta padre
        
            
            $table->foreign('carpeta_padre_id')// Define una columna 'carpeta_padre_id' sera una clave foránea
                  ->references('id')// Hace referencia a la columna 'id' de la tabla
                  ->on('carpetas')// Le idica a la base  de datos que  la relacion es con la tabla 'carpetas'
                  ->onUpdate('cascade') // Si se actualiza una carpeta, se actualizarán también sus subcarpetas
                  ->onDelete('cascade'); // Si se elimina una carpeta, se eliminarán también sus subcarpetas
            $table->foreign('user_id') //Se define la llave foreanea user_id
                  ->references('id') //Indica que apuntara a la columna id
                  ->on('users') // Indica que apunta a la tabla users
                  ->onUpdate('cascade') // Indica que las actualizaciones se realizaran en casacada con este id
                  ->onDelete('cascade'); // Indica que se eliminara tood en casacada con relacion  a este columna id 
                
               $table->timestamps(); // Crea las columnas 'created_at' y 'updated_at' para almacenar las fechas de creación y actualización de cada carpeta
        });
    }

    /**
     * Se crea el metodo que s e encraga de revertir la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('carpetas');
    }
};
