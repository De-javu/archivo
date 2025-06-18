<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubirArchivoRequest;
use App\Models\Archivo;
use Illuminate\Http\Request;
use App\Models\Carpeta;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ArchivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Carpeta $carpeta)
    {
       

    }
    

    /**
     * Display the specified resource.
     */
    public function show()
    {

    

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archivo $archivo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Archivo $archivo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archivo $archivo)
    {
        //
    }

   
    public function upload(SubirArchivoRequest $request, Carpeta $carpeta)
    {
        $carpeta_id = $request->validated()['carpeta_id'] ?? null; // Asegura que se obtenga el ID de carpeta validado
        $carpeta_nombre = $request->validated()['carpeta_nombre'] ?? null; // Asegura que se obtenga el nombre de carpeta validado
        $file = $request->validated()['file'] ?? null; // Asegura que se obtenga el archivo validado

        if ($file && $carpeta_id && $carpeta_nombre) { // Se realiza un ultimo filtro para validara que los datos necesarios estén presentes
            $file_Name = time() . '_' . $file->getClientOriginalName(); // Genera un nombre único para el archivo
            $ruta = $file->storeAs('archivo/'. $carpeta_id .'_'.$carpeta_nombre, $file_Name,'public'); // Guarda el archivo en la carpeta especificada en el disco 'public'
             
    
      // se carga a la base de datos.
      $archivo = new Archivo();
      $archivo->nombre = $file_Name;
      $archivo->ruta = $ruta; // Asigna la ruta del archivo
      $archivo->carpeta_id = $carpeta_id; // Asigna el ID de la carpeta


      $archivo->save(); // Guarda el modelo en la base de datos
    } 
 } 
}
