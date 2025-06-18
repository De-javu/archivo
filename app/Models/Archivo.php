<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = ['nombre','ruta', 'carpeta_id']; // Campos que se llenarán en la base de datos

    public function carpeta() // Relación muchos a uno 
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id'); // muchos archivos pertenecen a una carpeta
    }
}
