<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carpeta extends Model

 // se crea el modelo de la tabla carpetas para que almacene la informacion 
{
    protected $fillable = ['nombre', 'carpeta_padre_id']; // Son los campos que se llenaran en la base de datos

            public function carpetasHijas() // funcion de relacion uno a muchos
            {
              return $this->hasMany(Carpeta::class, 'carpeta_padre_id'); // una carpeta puede tener muchas carpetas hijas
            }

            public function archivos()// funcion de relacion uno a muchos
            {
              return $this->hasMany(Archivo::class, 'carpeta_id'); // un carpeta puede tener muchos archivos
            } 
  
}
