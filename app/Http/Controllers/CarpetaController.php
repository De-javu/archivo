<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarpetaRequest;
use App\Http\Requests\StoreSubCarpetaRequest;
use App\Models\Carpeta;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class CarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carpetas = Carpeta::all();
      return view('mi_unidad.index', compact('carpetas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarpetaRequest $request)
{
    Carpeta::create(['nombre' => $request->nombre]);
    
    return redirect()->route('mi_unidad.index')->with('success', 'Carpeta creada');
}

    /**
     * Display the specified resource.
     */
    public function show(Carpeta $carpeta)
    {
        $carpeta = Carpeta::with('archivos')->findOrFail($carpeta->id);
        return view('mi_unidad.show', compact('carpeta'));
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
    public function update(Request $request, Carpeta $carpeta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carpeta $carpeta)
    {
        //
    }

    public function subcarpeta(StoreSubCarpetaRequest $request, Carpeta $carpeta)
    {

        
       $carpeta = new Carpeta();
       $carpeta->nombre = $request->nombre;
       $carpeta->carpeta_padre_id = $request->carpeta_padre_id;
       $carpeta->save();

       return redirect()->route('mi_unidad.carpeta', $carpeta->id)->with('success', 'Subcarpeta creada');
    }
}
