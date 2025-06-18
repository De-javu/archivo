<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteCarpetaRequest;
use App\Http\Requests\StoreCarpetaRequest;
use App\Http\Requests\StoreSubCarpetaRequest;
use App\Models\Carpeta;


class CarpetaController extends Controller
{
    /**
     * Se encraga de mostrar las carpetas y archivos de la unidad del usuario.
     */
    public function index()
    {
        $carpetas = Carpeta::whereNull('carpeta_padre_id') // Obtiene las carpetas que no tienen carpeta padre
            ->with(['carpetasHijas', 'archivos']) // Carga las carpetas hijas y archivos relacionados
            ->get();// Obtiene todas las carpetas principales despues de aplicar las condiciones anteriores
      return view('mi_unidad.index', compact('carpetas')); // Retorna la vista con las carpetas y archivos
    }

    /**
     * Es el metod que se encarga de mostrar el formulario para crear una nueva carpeta.
     */
    public function create()
    {
        //
    }

    /**
     * Es el metodo que s eencarag de almacenar una nueva carpeta en la base de datos.
     */
    public function store(StoreCarpetaRequest $request)
{
    Carpeta::create(['nombre' => $request->nombre]);    // Crea una nueva carpeta con el nombre proporcionado en la solicitud, que recibe de un formualrio de creación de carpetas
    return redirect()->route('mi_unidad.index')->with('success', 'Carpeta creada'); // Redirige a la ruta 'mi_unidad.index' con un mensaje de éxito
}

    /**
     * Este metodo se encarga de mostrar una carpeta especifica y sus subcarpetas y archivos.
     */
    public function show(Carpeta $carpeta)
    {
        
        $carpeta = Carpeta::with('archivos') // Carga con los archivos
        ->findOrFail($carpeta->id);// Busca la carpeta por su ID y lanza una excepción si no se encuentra

        $subcarpetas = Carpeta::with('carpetasHijas') // Cargacon las subcarpetas
        ->where('carpeta_padre_id', $carpeta->id) // Filtra las subcarpetas que pertenecen a la carpeta actual
        ->get(); // Obtiene las subcarpetas de la carpeta actual
        
        $archivos = $carpeta->archivos;
        return view('mi_unidad.show', compact('carpeta', 'subcarpetas', 'archivos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carpeta $carpeta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCarpetaRequest $request, Carpeta $carpeta)
    {
       $carpeta = Carpeta::findOrFail($carpeta->id); // Busca la carpeta por su ID y lanza una excepción si no se encuentra
       $carpeta->nombre = $request->nombre;
       $carpeta->save();

       

       
      if($carpeta->carpeta_padre_id)
      {
        // si la carpeta no tiene una carpeta padre se redigira a la vista de la unidad
        return redirect()->route('mi_unidad.carpeta', $carpeta->carpeta_padre_id)
        ->with('success', 'Carpeta actualizada'); // Redirige a la ruta 'mi_unidad.index' con un mensaje de éxito
      }
      else
      {
        return redirect()->route('mi_unidad.index')
        ->with('success', 'Carpeta actualizada'); // Redirige a la ruta 'mi_unidad.index' con un mensaje de éxito
      }

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteCarpetaRequest $request, Carpeta $carpeta)
    {
       

        $carpeta = Carpeta::findOrFail($carpeta->id); // Busca la carpeta por su ID y lanza una excepción si no se encuentra
        $carpeta->delete(); // Elimina la carpeta de la base de datos

        return redirect()->route('mi_unidad.index')->with('success', 'Carpeta eliminada'); // Redirige a la ruta 'mi_unidad.index' con un mensaje de éxito
    }

    public function subcarpeta(StoreSubCarpetaRequest $request, Carpeta $carpeta)
  

   {
    // Calcula la profundidad de la subcarpeta
    $profundidad = 1;
    $padre = Carpeta::find($request->carpeta_padre_id);
    while ($padre && $padre->carpeta_padre_id) {
        $profundidad++;
        $padre = Carpeta::find($padre->carpeta_padre_id);
    }

    // Limita la profundidad máxima (por ejemplo, 4)
    if ($profundidad >= 4) {
        return redirect()->back()->withErrors(['nombre' => 'No puedes crear más de 4 niveles de subcarpetas.']);
    }

    $carpeta = new Carpeta();
    $carpeta->nombre = $request->nombre;
    $carpeta->carpeta_padre_id = $request->carpeta_padre_id;
    $carpeta->save();

    return redirect()->route('mi_unidad.carpeta', $carpeta->id)->with('success', 'Subcarpeta creada');
    }
}
