<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
// No more controllers for CRUD routes! All migrated to Livewire.

// Livewire full-page components (Fase 1 — Catálogos)
use App\Livewire\AeropuertoIndex;
use App\Livewire\AeropuertoForm;
use App\Livewire\AeropuertoShow;
use App\Livewire\RutaIndex;
use App\Livewire\RutaForm;
use App\Livewire\RutaShow;
use App\Livewire\PasajeroIndex;
use App\Livewire\PasajeroForm;
use App\Livewire\PasajeroShow;
use App\Livewire\PersonaIndex;
use App\Livewire\PersonaForm;
use App\Livewire\PersonaShow;
use App\Livewire\AeronaveIndex;
use App\Livewire\AeronaveForm;
use App\Livewire\AeronaveShow;
use App\Livewire\EmpleadoIndex;
use App\Livewire\EmpleadoForm;
use App\Livewire\EmpleadoShow;
use App\Livewire\PilotoIndex;
use App\Livewire\PilotoForm;
use App\Livewire\PilotoShow;
use App\Livewire\MantenimientoIndex;
use App\Livewire\MantenimientoForm;
use App\Livewire\MantenimientoShow;
use App\Livewire\CargaIndex;
use App\Livewire\CargaForm;
use App\Livewire\CargaShow;
use App\Livewire\VueloIndex;
use App\Livewire\VueloForm;
use App\Livewire\VueloShow;
use App\Livewire\VueloDespachar;
use App\Livewire\ReservaIndex;
use App\Livewire\ReservaForm;
use App\Livewire\ReservaShow;
use App\Livewire\VentaIndex;
use App\Livewire\VentaForm;
use App\Livewire\VentaShow;
use App\Livewire\FacturaIndex;
use App\Livewire\FacturaShow;
use App\Livewire\UsuarioIndex;
use App\Livewire\UsuarioForm;
use App\Livewire\RolIndex;
use App\Livewire\RolForm;
use App\Livewire\DashboardIndex;
use App\Livewire\OperacionesPanel;
use App\Livewire\OperacionesReporte;
use App\Livewire\ReportesIndex;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/operaciones', OperacionesPanel::class)->name('operaciones')->middleware('can:reportes.ver');
    Route::get('/operaciones/reporte', OperacionesReporte::class)->name('operaciones.reporte')->middleware('can:reportes.ver');

    // Reportes (Ventas / Reservas / Vuelos) con exportación a PDF
    Route::middleware('can:reportes.ver')->group(function () {
        Route::get('/reportes', ReportesIndex::class)->name('reportes');
        Route::get('reportes/ventas/pdf', [\App\Http\Controllers\PdfController::class, 'reporteVentas'])->name('reportes.ventas.pdf');
        Route::get('reportes/reservas/pdf', [\App\Http\Controllers\PdfController::class, 'reporteReservas'])->name('reportes.reservas.pdf');
        Route::get('reportes/vuelos/pdf', [\App\Http\Controllers\PdfController::class, 'reporteVuelos'])->name('reportes.vuelos.pdf');
    });

    // ── Fase 3 — Operación (Vuelos) ──────────────────────────────────────
    Route::middleware('can:vuelos.gestionar')->group(function () {
        Route::get('vuelos', VueloIndex::class)->name('vuelos.index');
        Route::get('vuelos/crear', VueloForm::class)->name('vuelos.create');
        Route::get('vuelos/{vuelo}/editar', VueloForm::class)->name('vuelos.edit');
        Route::get('vuelos/{vuelo}', VueloShow::class)->name('vuelos.show');
    });
    
    Route::middleware('can:operaciones.despachar')->group(function () {
        Route::get('vuelos/{vuelo}/despachar', VueloDespachar::class)->name('vuelos.despachar.form');
    });
    
    // ── Fase 4 — Comercial (Reservas) ────────────────────────────────────
    Route::middleware('can:reservas.gestionar')->group(function () {
        Route::get('reservas', ReservaIndex::class)->name('reservas.index');
        Route::get('reservas/crear', ReservaForm::class)->name('reservas.create');
        Route::get('reservas/{reserva}/editar', ReservaForm::class)->name('reservas.edit');
        Route::get('reservas/{reserva}', ReservaShow::class)->name('reservas.show');
        
        Route::get('ventas', VentaIndex::class)->name('ventas.index');
        Route::get('ventas/crear', VentaForm::class)->name('ventas.create');
        Route::get('ventas/cobrar', \App\Livewire\CobroPendiente::class)->name('ventas.cobrar');
        Route::get('ventas/{venta}', VentaShow::class)->name('ventas.show');
    });
    
    // Facturación
    Route::middleware('can:reservas.gestionar')->group(function () {
        Route::get('facturas', FacturaIndex::class)->name('facturas.index');
        Route::get('facturas/{factura}', FacturaShow::class)->name('facturas.show');
        
        // PDF Routes
        Route::get('boletos/{boleto}/pdf', [\App\Http\Controllers\PdfController::class, 'imprimirBoleto'])->name('boletos.pdf');
        Route::get('facturas/{factura}/pdf', [\App\Http\Controllers\PdfController::class, 'imprimirFactura'])->name('facturas.pdf');
    });

    // ── Fase 1 — Catálogos (Livewire full-page) ─────────────────────────
    Route::middleware('can:catalogos.gestionar')->group(function () {
        // Aeropuertos
        Route::get('aeropuertos', AeropuertoIndex::class)->name('aeropuertos.index');
        Route::get('aeropuertos/crear', AeropuertoForm::class)->name('aeropuertos.create');
        Route::get('aeropuertos/{aeropuerto}/editar', AeropuertoForm::class)->name('aeropuertos.edit');
        Route::get('aeropuertos/{aeropuerto}', AeropuertoShow::class)->name('aeropuertos.show');

        // Rutas
        Route::get('rutas', RutaIndex::class)->name('rutas.index');
        Route::get('rutas/crear', RutaForm::class)->name('rutas.create');
        Route::get('rutas/{ruta}/editar', RutaForm::class)->name('rutas.edit');
        Route::get('rutas/{ruta}', RutaShow::class)->name('rutas.show');

        // Pasajeros
        Route::get('pasajeros', PasajeroIndex::class)->name('pasajeros.index');
        Route::get('pasajeros/crear', PasajeroForm::class)->name('pasajeros.create');
        Route::get('pasajeros/{pasajero}/editar', PasajeroForm::class)->name('pasajeros.edit');
        Route::get('pasajeros/{pasajero}', PasajeroShow::class)->name('pasajeros.show');

        // Personas
        Route::get('personas', PersonaIndex::class)->name('personas.index');
        Route::get('personas/crear', PersonaForm::class)->name('personas.create');
        Route::get('personas/{persona}/editar', PersonaForm::class)->name('personas.edit');
        Route::get('personas/{persona}', PersonaShow::class)->name('personas.show');
    });

    // Aeronaves (Livewire full-page)
    Route::middleware('can:flota.gestionar')->group(function () {
        Route::get('aeronaves', AeronaveIndex::class)->name('aeronaves.index');
        Route::get('aeronaves/crear', AeronaveForm::class)->name('aeronaves.create');
        Route::get('aeronaves/{aeronave}/editar', AeronaveForm::class)->name('aeronaves.edit');
        Route::get('aeronaves/{aeronave}', AeronaveShow::class)->name('aeronaves.show');
    });

    // ── Fase 2 — Personal (Livewire full-page) ────────────────────────────
    Route::middleware('can:personal.gestionar')->group(function () {
        Route::get('empleados', EmpleadoIndex::class)->name('empleados.index');
        Route::get('empleados/crear', EmpleadoForm::class)->name('empleados.create');
        Route::get('empleados/{empleado}/editar', EmpleadoForm::class)->name('empleados.edit');
        Route::get('empleados/{empleado}', EmpleadoShow::class)->name('empleados.show');

        Route::get('pilotos', PilotoIndex::class)->name('pilotos.index');
        Route::get('pilotos/crear', PilotoForm::class)->name('pilotos.create');
        Route::get('pilotos/{piloto}/editar', PilotoForm::class)->name('pilotos.edit');
        Route::get('pilotos/{piloto}', PilotoShow::class)->name('pilotos.show');
    });

    // ── Fase 3 — Operación (Livewire full-page, excepto Vuelos) ──────────
    Route::middleware('can:mantenimiento.gestionar')->group(function () {
        Route::get('mantenimientos', MantenimientoIndex::class)->name('mantenimientos.index');
        Route::get('mantenimientos/crear', MantenimientoForm::class)->name('mantenimientos.create');
        Route::get('mantenimientos/{mantenimiento}/editar', MantenimientoForm::class)->name('mantenimientos.edit');
        Route::get('mantenimientos/{mantenimiento}', MantenimientoShow::class)->name('mantenimientos.show');
    });

    Route::middleware('can:reservas.gestionar')->group(function () {
        Route::get('carga', CargaIndex::class)->name('carga.index');
        Route::get('carga/crear', CargaForm::class)->name('carga.create');
        Route::get('carga/{carga}/editar', CargaForm::class)->name('carga.edit');
        Route::get('carga/{carga}', CargaShow::class)->name('carga.show');
    });

    // ── Fase 5 — Administración (Usuarios y Roles RBAC) ────────────────
    Route::middleware('can:usuarios.gestionar')->group(function () {
        Route::get('usuarios', UsuarioIndex::class)->name('usuarios.index');
        Route::get('usuarios/crear', UsuarioForm::class)->name('usuarios.create');
        Route::get('usuarios/{usuario}/editar', UsuarioForm::class)->name('usuarios.edit');
        
        Route::get('roles', RolIndex::class)->name('roles.index');
        Route::get('roles/crear', RolForm::class)->name('roles.create');
        Route::get('roles/{rol}/editar', RolForm::class)->name('roles.edit');
    });

    Route::fallback(function() {
        return view('pages/utility/404');
    });
});
