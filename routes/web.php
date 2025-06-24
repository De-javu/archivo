<?php

use App\Http\Controllers\CarpetaController;
use App\Http\Controllers\ArchivoController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Models\Archivo;
use App\Models\Carpeta;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

    Route::middleware((['auth']))->prefix('/dashboard')->group(function () {
    Route::get('/mi_unidad',[ CarpetaController::class, 'index'])->name('mi_unidad.index');
    Route::post('/mi_unidad', [CarpetaController::class, 'store'])->name('mi_unidad.store');
    Route::get('/mi_unidad/carpeta/{carpeta}',[CarpetaController::class, 'show'])->name('mi_unidad.carpeta');
    Route::post('/mi_unidad/carpeta/{carpeta}',[CarpetaController::class, 'subcarpeta'])->name('mi_unidad.subcarpeta');
    Route::put('/mi_unidad/carpeta/{carpeta}', [CarpetaController::class, 'update'])->name('mi_unidad.update');
    Route::delete('/mi_unidad/carpeta/{carpeta}', [CarpetaController::class, 'destroy'])->name('mi_unidad.destroy');


    //Rutas para archivos a una carpeta
    Route::post('/mi_unidad/{carpeta}/subir_archivo', [ArchivoController::class, 'upload'])->name('mi_unidad.subir_archivo');
    Route::delete('/mi_unidad/archivo/{archivo}', [ArchivoController::class,'destroy'])->name('mi_unidad.eliminar_archivo');
});


require __DIR__.'/auth.php';
