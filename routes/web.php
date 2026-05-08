<?php

use App\Http\Controllers\Admin\AccesosController;
use App\Http\Controllers\Admin\ActividadController;
use App\Http\Controllers\Admin\AsistenciaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\FinanzasController;
use App\Http\Controllers\Admin\QrCampaignController;
use App\Http\Controllers\Admin\QrTokenController;
use App\Http\Controllers\Admin\ReconocimientoController;
use App\Http\Controllers\Admin\TribuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ActividadRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SenderistaRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:Administrador|Supervisor'])
    ->name('admin.dashboard');

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador|Supervisor|Líder'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->whereNumber('user')->name('admin.users.show');

    Route::get('/departamentos', [DepartamentoController::class, 'index'])->name('admin.departamentos.index');
    Route::get('/departamentos/{departamento}', [DepartamentoController::class, 'show'])->whereNumber('departamento')->name('admin.departamentos.show');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador|Supervisor'])->group(function () {
    Route::get('/accesos', [AccesosController::class, 'index'])->name('admin.accesos.index');
    Route::put('/accesos/{user}/password', [AccesosController::class, 'updatePassword'])->whereNumber('user')->name('admin.accesos.password');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador|Supervisor'])->group(function () {
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->whereNumber('user')->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->whereNumber('user')->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->whereNumber('user')->name('admin.users.destroy');
    Route::get('/users/{user}/convert', [UserController::class, 'convert'])->whereNumber('user')->name('admin.users.convert');
    Route::post('/users/{user}/convert', [UserController::class, 'convertStore'])->whereNumber('user')->name('admin.users.convert.store');

    Route::get('/departamentos/create', [DepartamentoController::class, 'create'])->name('admin.departamentos.create');
    Route::post('/departamentos', [DepartamentoController::class, 'store'])->name('admin.departamentos.store');
    Route::get('/departamentos/{departamento}/edit', [DepartamentoController::class, 'edit'])->whereNumber('departamento')->name('admin.departamentos.edit');
    Route::put('/departamentos/{departamento}', [DepartamentoController::class, 'update'])->whereNumber('departamento')->name('admin.departamentos.update');
    Route::delete('/departamentos/{departamento}', [DepartamentoController::class, 'destroy'])->whereNumber('departamento')->name('admin.departamentos.destroy');

    Route::get('/actividades', [ActividadController::class, 'index'])->name('admin.actividades.index');
    Route::get('/actividades/create', [ActividadController::class, 'create'])->name('admin.actividades.create');
    Route::post('/actividades', [ActividadController::class, 'store'])->name('admin.actividades.store');
    Route::get('/actividades/{actividad}', [ActividadController::class, 'show'])->whereNumber('actividad')->name('admin.actividades.show');
    Route::post('/actividades/{actividad}/close', [ActividadController::class, 'close'])->whereNumber('actividad')->name('admin.actividades.close');

    Route::get('/reconocimientos', [ReconocimientoController::class, 'index'])->name('admin.reconocimientos.index');
    Route::get('/reconocimientos/create', [ReconocimientoController::class, 'create'])->name('admin.reconocimientos.create');
    Route::post('/reconocimientos', [ReconocimientoController::class, 'store'])->name('admin.reconocimientos.store');
    Route::post('/reconocimientos/cumpleanos', [ReconocimientoController::class, 'sendBirthdays'])->name('admin.reconocimientos.birthdays.send');
    Route::delete('/reconocimientos/{reconocimiento}', [ReconocimientoController::class, 'destroy'])->whereNumber('reconocimiento')->name('admin.reconocimientos.destroy');

    Route::resource('qr-campaigns', QrCampaignController::class)->names('admin.qr-campaigns');
    Route::post('/qr-campaigns/{qrCampaign}/tribus', [TribuController::class, 'store'])
        ->whereNumber('qrCampaign')
        ->name('admin.qr-campaigns.tribus.store');
    Route::delete('/qr-campaigns/{qrCampaign}/tribus/{tribu}', [TribuController::class, 'destroy'])
        ->whereNumber('qrCampaign')
        ->whereNumber('tribu')
        ->name('admin.qr-campaigns.tribus.destroy');
    Route::put('/qr-campaigns/{qrCampaign}/registrations/{registration}/tribu', [TribuController::class, 'assign'])
        ->whereNumber('qrCampaign')
        ->whereNumber('registration')
        ->name('admin.qr-campaigns.registrations.tribu');

    Route::get('/qr-tokens', [QrTokenController::class, 'index'])->name('admin.qr-tokens.index');
    Route::get('/qr-tokens/create', [QrTokenController::class, 'create'])->name('admin.qr-tokens.create');
    Route::post('/qr-tokens', [QrTokenController::class, 'store'])->name('admin.qr-tokens.store');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador'])->group(function () {
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador|Supervisor|Encargado|Líder'])->group(function () {
    Route::get('/actividades/mis', [ActividadController::class, 'mis'])->name('admin.actividades.mis');
    Route::get('/actividades/departamento/{departamentoActividad}/asistencia', [AsistenciaController::class, 'index'])
        ->whereNumber('departamentoActividad')
        ->name('admin.actividades.asistencia');
    Route::post('/actividades/departamento/{departamentoActividad}/asistencia/{asistencia}', [AsistenciaController::class, 'update'])
        ->whereNumber('departamentoActividad')
        ->whereNumber('asistencia')
        ->name('admin.actividades.asistencia.update');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:Administrador|Supervisor|Finanzas'])->group(function () {
    Route::get('/finanzas', [FinanzasController::class, 'index'])->name('admin.finanzas.index');
    Route::get('/finanzas/departamentos/{departamento}', [FinanzasController::class, 'departamento'])
        ->whereNumber('departamento')
        ->name('admin.finanzas.departamento');
    Route::get('/finanzas/create', [FinanzasController::class, 'create'])->name('admin.finanzas.create');
    Route::post('/finanzas', [FinanzasController::class, 'store'])->name('admin.finanzas.store');
    Route::delete('/finanzas/{movimiento}', [FinanzasController::class, 'destroy'])->whereNumber('movimiento')->name('admin.finanzas.destroy');

    Route::post('/actividades/departamento/{departamentoActividad}/abonos', [ActividadController::class, 'abonar'])
        ->whereNumber('departamentoActividad')
        ->name('admin.actividades.abonos.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/registro/senderista/completado', [SenderistaRegistrationController::class, 'success'])
    ->name('senderista.register.success');

Route::get('/registro/actividad/completado', [ActividadRegistrationController::class, 'success'])
    ->name('actividad.register.success');
Route::get('/registro/actividad/{token}', [ActividadRegistrationController::class, 'create'])
    ->name('actividad.register.create');
Route::post('/registro/actividad/{token}', [ActividadRegistrationController::class, 'store'])
    ->name('actividad.register.store');

Route::middleware('senderista.token')->group(function () {
    Route::get('/registro/senderista/{token}', [SenderistaRegistrationController::class, 'create'])
        ->name('senderista.register.create');
    Route::post('/registro/senderista/{token}', [SenderistaRegistrationController::class, 'store'])
        ->name('senderista.register.store');
});

require __DIR__.'/auth.php';
