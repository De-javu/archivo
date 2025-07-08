<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteCarpetaRequest;
use App\Http\Requests\StoreCarpetaRequest;
use App\Http\Requests\StoreSubCarpetaRequest;
use App\Models\Carpeta;
use Illuminate\Support\Facades\Storage;


class CarpetaController extends Controller
{
    /**
     * Se encarga de mostrar las carpetas y archivos de la unidad del usuario.
     */
    public function index()
    {
        $carpetas = Carpeta::whereNull('carpeta_padre_id') // Obtiene las carpetas que no tienen carpeta padre
            ->where('user_id', auth()->id()) // Filtra las carpetas para que solo muestre las del usuario autenticado
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
     * Es el metodo que se encarga de almacenar una nueva carpeta en la base de datos.
     */
    public function store(StoreCarpetaRequest $request)
{
    // Crea una nueva carpeta con el nombre proporcionado en la solicitud, que recibe de un formualrio de creación de carpetas
    Carpeta::create([ // Se crea llama el metodo crear por medio del ORM
        'nombre' => $request->nombre, // Se recoge el nombre de la validacion
        'user_id' => auth()->id(), // Se recoge el usuario logeadop, para validar que es el mimos que crea
        'carpeta_padre_id' => $request->carpeta_padre_id ?? null, // Si se crea una sub, carpeta se ahblita de lo contrario enteinde que no se crea y lo llama null

]);
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
     * Se crea un controlador para la actualizacion de una carpeta
     */
    public function update(StoreCarpetaRequest $request, Carpeta $carpeta)
    {
       $carpeta = Carpeta::findOrFail($carpeta->id); // Busca la carpeta por su ID y lanza una excepción si no se encuentra
       $carpeta->nombre = $request->nombre; // Asigna el nuevo nombre de la carpeta desde la solicitud
       $carpeta->save(); // Guarda los cambios en la carpeta en la base de datos

      if($carpeta->carpeta_padre_id)
      {
        // si la carpeta no tiene una carpeta padre se redigira a la vista de la unidad
        return redirect()->route('mi_unidad.carpeta', $carpeta)
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
       // Se valida que el usuario autenticado sea el propietario de la carpeta antes de eliminarla
        if ($carpeta->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403, 'No tienes permiso para eliminar esta carpeta.');
    }

        $carpeta = Carpeta::findOrFail($carpeta->id); // Busca la carpeta por su ID y lanza una excepción si no se encuentra
        Storage::disk('public')->deleteDirectory('archivo/'.$carpeta->id.'_'.$carpeta->nombre ); // toca costruir la ruta para eliminar el directorio para eliminar desde el directorio
        $carpeta->delete(); // Elimina la carpeta de la base de datos

        return redirect()->route('mi_unidad.index')->with('success', 'Carpeta eliminada'); // Redirige a la ruta 'mi_unidad.index' con un mensaje de éxito
    }

    public function subcarpeta(StoreSubCarpetaRequest $request, Carpeta $carpeta)


   {
    // Calcula la profundidad de la subcarpeta
    $profundidad = 1; // contador
    $padre = Carpeta::find($request->carpeta_padre_id); // Busca la carpeta padre de la subcarpeta seleccionada
    while ($padre && $padre->carpeta_padre_id) {            // Mientras haya una carpeta padre, sigue subiendo en la jerarquía
        $profundidad++;                                     // Incrementa la profundidad
        $padre = Carpeta::find($padre->carpeta_padre_id); // Busca la carpeta padre de la carpeta actual
    }

    // Limita la profundidad máxima (por ejemplo, 4)
    if ($profundidad >= 4) {
        return redirect()->back()->withErrors(['nombre' => 'No puedes crear más de 4 niveles de subcarpetas.']);
    }

    $carpeta = new Carpeta(); // Crea una nueva instancia de Carpeta
    $carpeta->nombre = $request->nombre; // Asigna el nombre de la subcarpeta desde la solicitud
    $carpeta->carpeta_padre_id = $request->carpeta_padre_id;// Asigna el ID de la carpeta padre desde la solicitud
    $carpeta->user_id = auth()->id(); // Asigna el ID del usuario autenticado a la carpeta
    $carpeta->save(); // Guarda la nueva subcarpeta en la base de datos

    return redirect()->route('mi_unidad.carpeta', $carpeta->id)->with('success', 'Subcarpeta creada');
    }
}
