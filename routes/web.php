<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;

Route::get('/', function () {
    return view('home');
});

// Vistas (Cargan instantáneamente)
Route::get('/inventario', [VehiculoController::class, 'inventario'])->name('inventario');
Route::get('/detalle/{id}', [VehiculoController::class, 'detalle'])->name('detalle');

// APIs (Devuelven JSON en segundo plano)
Route::get('/api/inventario-data', [VehiculoController::class, 'fetchInventarioData'])->name('api.inventario');
Route::get('/api/detalle-data/{id}', [VehiculoController::class, 'fetchDetalleData'])->name('api.detalle');

// Ruta para procesar la solicitud de cotización desde el formulario
Route::post('/solicitar-cotizacion', [VehiculoController::class, 'solicitarCotizacion'])->name('solicitar.cotizacion');
Route::post('/solicitar-financiamiento', [VehiculoController::class, 'solicitarFinanciamiento'])->name('solicitar.financiamiento');
