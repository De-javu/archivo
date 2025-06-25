<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubirArchivoRequest;
use App\Models\Archivo;
use Illuminate\Http\Request;
use App\Models\Carpeta;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        
        Storage::disk('public')->delete($archivo->ruta); // Se encarga d eeliminar el archivo d ela carpeta public
        $archivo->delete(); // elimina en la base  de datos
        return redirect()->back()->with('secces', 'Archivo eliminado correctamnete'); //redirecciona a ala vista deseada

    }

   
    public function upload(SubirArchivoRequest $request, Carpeta $carpeta)
    {
        //Se alamacenan en varaibles los datios recibidos desde el request para su manipulacion 
        $carpeta_id = $request->validated()['carpeta_id'] ?? null; // Asegura que se obtenga el ID de carpeta validado
        $carpeta_nombre = $request->validated()['carpeta_nombre'] ?? null; // Asegura que se obtenga el nombre de carpeta validado
        $file = $request->validated()['file'] ?? null; // Asegura que se obtenga el archivo validado

        // Se realiza un ultimo filtro para validara que los datos necesarios estén presentes
        if ($file && $carpeta_id && $carpeta_nombre) { 
              
            $nombre_carpeta_slug = $carpeta_id .'_'. Str::slug($carpeta_nombre); // Se utiliza el metodo slug, para limpiar en nombre d elas carpetas.
            $nombre_base_slug= Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)); // Se toma el nombre original y se limpia y se almacena sin la extencion
            $nombre_del_archivo = time() . '_' .($nombre_base_slug ?:'archivo'); // Se asigna funcion para crear una marca de tiempo, y se concatena con el nombre del archivo limpio si este no tiene nombre se deja por defecto archivo, se almacena en la variable
            $extension = $file->getClientOriginalExtension(); // Se utiliza para estraer la extencion del archivo original, esto grantiza su almacenamiento
            $nombre_final= $nombre_del_archivo . '.' . $extension; // Se construlle el nombre final como se almacenara el archivo

            $ruta = $file->storeAs('archivo/'. $nombre_carpeta_slug, $nombre_final,'public'); // Guarda el archivo en la carpeta especificada en el disco 'public'
             
    
      //se carga a la base de datos.
      $archivo = new Archivo();
      $archivo->nombre = $nombre_final;
      $archivo->ruta = $ruta; // Asigna la ruta del archivo
      $archivo->carpeta_id = $carpeta_id; // Asigna el ID de la carpeta


      $archivo->save(); // Guarda el modelo en la base de datos
    } 
 } 
}
