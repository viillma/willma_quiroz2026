<?php

use App\Enums\Permission;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DesignFileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, 'role:administrador,disenador,asistente,recepcionista', 'permission:panel.ver'])
    ->group(function () {
        Route::inertia('dashboard', 'Dashboard')
            ->middleware('permission:'.Permission::PanelVer->value)
            ->name('dashboard');

        Route::middleware('permission:'.Permission::ServiciosGestionar->value)->group(function () {
            Route::resource('servicios', ServicioController::class);
        });

        Route::middleware('permission:'.Permission::ClientesGestionar->value)->group(function () {
            Route::resource('clientes', ClientController::class);
        });

        Route::middleware('permission:'.Permission::PedidosGestionar->value)->group(function () {
            Route::resource('pedidos', OrderController::class);
        });

        Route::inertia('catalogo', 'catalog/Index')
            ->name('catalogo.index');

        Route::middleware('permission:'.Permission::PedidosVer->value)->group(function () {
            Route::get('pedidos/{order}/disenos', [DesignFileController::class, 'index'])->name('disenos.index');
            Route::post('pedidos/{order}/disenos', [DesignFileController::class, 'store'])->name('disenos.store');
            Route::get('pedidos/{order}/disenos/{designFile}/download', [DesignFileController::class, 'download'])->name('disenos.download');
            Route::post('pedidos/{order}/disenos/{designFile}/approve', [DesignFileController::class, 'approve'])->name('disenos.approve');
            Route::delete('pedidos/{order}/disenos/{designFile}', [DesignFileController::class, 'destroy'])->name('disenos.destroy');
        });

        Route::middleware('permission:'.Permission::PagosVer->value)->group(function () {
            Route::get('pedidos/{order}/pagos', [PaymentController::class, 'index'])->name('pagos.index');
            Route::post('pedidos/{order}/pagos', [PaymentController::class, 'store'])->name('pagos.store');
            Route::post('pedidos/{order}/pagos/{payment}/confirm', [PaymentController::class, 'confirm'])->name('pagos.confirm');
            Route::post('pedidos/{order}/pagos/{payment}/reject', [PaymentController::class, 'reject'])->name('pagos.reject');
            Route::delete('pedidos/{order}/pagos/{payment}', [PaymentController::class, 'destroy'])->name('pagos.destroy');
        });

        Route::middleware('permission:'.Permission::ReportesVer->value)->group(function () {
            Route::get('reportes', [ReportController::class, 'index'])->name('reportes.index');
            Route::get('reportes/ingresos', [ReportController::class, 'income'])->name('reportes.income');
            Route::get('reportes/servicios', [ReportController::class, 'services'])->name('reportes.services');
            Route::get('reportes/clientes', [ReportController::class, 'clients'])->name('reportes.clients');
            Route::get('reportes/disenadores', [ReportController::class, 'designers'])->name('reportes.designers');
            Route::get('reportes/morosos', [ReportController::class, 'morosos'])->name('reportes.morosos');
        });

        Route::middleware('permission:'.Permission::UsuariosGestionar->value)->group(function () {
            Route::get('usuarios', [UsersController::class, 'index'])->name('usuarios.index');
            Route::get('usuarios/create', [UsersController::class, 'create'])->name('usuarios.create');
            Route::post('usuarios', [UsersController::class, 'store'])->name('usuarios.store');
            Route::get('usuarios/{user}/edit', [UsersController::class, 'edit'])->name('usuarios.edit');
            Route::put('usuarios/{user}', [UsersController::class, 'update'])->name('usuarios.update');
            Route::delete('usuarios/{user}', [UsersController::class, 'destroy'])->name('usuarios.destroy');
            Route::post('usuarios/{user}/toggle-active', [UsersController::class, 'toggleActive'])->name('usuarios.toggle-active');
            Route::resource('roles', RoleController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        });
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
