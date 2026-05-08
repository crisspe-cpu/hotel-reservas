<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// ADMIN
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HabitacionController;
use App\Http\Controllers\Admin\TipoHabitacionController;
use App\Http\Controllers\Admin\UsuarioController;

// RECEPCIONISTA
use App\Http\Controllers\Recepcionista\ClienteController;
use App\Http\Controllers\Recepcionista\ReservaController;
use App\Http\Controllers\Recepcionista\PagoController;
use App\Http\Controllers\Recepcionista\BoletaController;

/*
|--------------------------------------------------------------------------
| Página principal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});


/*
|--------------------------------------------------------------------------
| Dashboard según rol
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->get('/dashboard', function () {

    $user = Auth::user();

    // ADMIN
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    // RECEPCIONISTA
    return redirect()->route('recepcionista.dashboard');

})->name('dashboard');


/*
|--------------------------------------------------------------------------
| PERFIL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| RECEPCIONISTA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:recepcionista'])
    ->prefix('recepcionista')
    ->name('recepcionista.')
    ->group(function () {

    Route::get('/dashboard', function () {
        return view('recepcionista.dashboard');
    })->name('dashboard');

    Route::resource('clientes', ClienteController::class);

    Route::resource('reservas', ReservaController::class);

    Route::resource('pagos', PagoController::class)
        ->except(['edit', 'update', 'show']);

    Route::resource('boletas', BoletaController::class)
        ->only(['index', 'store', 'show']);
});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('habitaciones', HabitacionController::class);

    Route::resource('tipos', TipoHabitacionController::class);

    Route::resource('usuarios', UsuarioController::class)
        ->except(['show']);
});


require __DIR__.'/auth.php';