<?php

use App\Enums\Permission;
use App\Http\Controllers\Admin\Web\CarouselController;
use App\Http\Controllers\Admin\Web\MediaController;
use App\Http\Controllers\Admin\Web\SettingsController as WebSettingsController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignFileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortafolioController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])
    ->name('home');

Route::get('servicios', [PublicController::class, 'servicios'])
    ->name('public.servicios');

Route::get('servicios/{slug}', [PublicController::class, 'servicio'])
    ->name('public.servicios.show');

Route::get('catalogo', [PublicController::class, 'catalogo'])
    ->name('public.catalogo');

Route::get('portafolio', [PublicController::class, 'portafolio'])
    ->name('public.portafolio');

Route::get('nosotros', [PublicController::class, 'nosotros'])
    ->name('public.nosotros');

Route::get('contacto', [PublicController::class, 'contacto'])
    ->name('public.contacto');

Route::post('contacto', [PublicController::class, 'contactoEnvio'])
    ->name('public.contacto.enviar')
    ->middleware('throttle:6,1');

Route::get('preguntas-frecuentes', [PublicController::class, 'faq'])
    ->name('public.faq');

Route::get('robots.txt', [PublicController::class, 'robots'])
    ->name('public.robots');

Route::get('sitemap.xml', [PublicController::class, 'sitemap'])
    ->name('public.sitemap');

/*
|--------------------------------------------------------------------------
| Role entry points
|--------------------------------------------------------------------------
|
| Each role has a dedicated entry URL that funnels the user into the
| shared panel. Access is protected by the role middleware.
|
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('admin', fn () => redirect()->route('dashboard'))
        ->middleware('role:administrador')
        ->name('admin');

    Route::get('recepcion', fn () => redirect()->route('dashboard'))
        ->middleware('role:recepcionista,administrador')
        ->name('recepcion');

    Route::get('diseno', fn () => redirect()->route('dashboard'))
        ->middleware('role:disenador,administrador')
        ->name('diseno');
});

Route::middleware([
    'auth',
    'verified',
    'active',
    'role:administrador,disenador,recepcionista',
    'permission:'.Permission::PanelVer->value,
])->prefix('panel')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('servicios', ServicioController::class)
        ->middleware('permission:'.Permission::ServiciosGestionar->value);

    Route::post('servicios/{servicio}/toggle', [ServicioController::class, 'toggle'])
        ->name('servicios.toggle')
        ->middleware('permission:'.Permission::ServiciosGestionar->value);

    Route::resource('clientes', ClientController::class)
        ->middleware('permission:'.Permission::ClientesGestionar->value);

    Route::post('clientes/{cliente}/toggle', [ClientController::class, 'toggle'])
        ->name('clientes.toggle')
        ->middleware('permission:'.Permission::ClientesGestionar->value);

    Route::resource('pedidos', OrderController::class)
        ->middleware('permission:'.Permission::PedidosGestionar->value);

    Route::resource('cotizaciones', CotizacionController::class)
        ->parameters(['cotizaciones' => 'cotizacion'])
        ->middleware('permission:'.Permission::CotizacionesGestionar->value);

    Route::post('cotizaciones/{cotizacion}/duplicar', [CotizacionController::class, 'duplicate'])
        ->name('cotizaciones.duplicar')
        ->middleware('permission:'.Permission::CotizacionesGestionar->value);

    Route::post('cotizaciones/{cotizacion}/convertir', [CotizacionController::class, 'convertToOrder'])
        ->name('cotizaciones.convertir')
        ->middleware('permission:'.Permission::CotizacionesGestionar->value);

    Route::get('cotizaciones/{cotizacion}/pdf', [CotizacionController::class, 'pdf'])
        ->name('cotizaciones.pdf')
        ->middleware('permission:'.Permission::CotizacionesVer->value);

    Route::resource('portafolio', PortafolioController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('permission:'.Permission::DiseñosGestionar->value);

    Route::post('portafolio/{portafolio}/publicar', [PortafolioController::class, 'togglePublish'])
        ->name('portafolio.publicar')
        ->middleware('permission:'.Permission::DiseñosGestionar->value);

    Route::delete('portafolio/{portafolio}/imagen/{image}', [PortafolioController::class, 'destroyImage'])
        ->name('portafolio.imagen.destroy')
        ->middleware('permission:'.Permission::DiseñosGestionar->value);

    Route::get('catalogo', [CatalogoController::class, 'index'])
        ->name('catalogo.index')
        ->middleware('permission:'.Permission::CatalogoVer->value);

    Route::get('catalogo/publico', [CatalogoController::class, 'publico'])
        ->name('catalogo.publico');

    Route::post(
        'catalogo',
        [CatalogoController::class, 'store']
    )->name('catalogo.store')
        ->middleware('permission:'.Permission::CatalogoCrear->value);

    Route::put(
        'catalogo/{catalogoDiseno}',
        [CatalogoController::class, 'update']
    )->name('catalogo.update')
        ->middleware('permission:'.Permission::CatalogoEditar->value);

    Route::delete(
        'catalogo/{catalogoDiseno}',
        [CatalogoController::class, 'destroy']
    )->name('catalogo.destroy')
        ->middleware('permission:'.Permission::CatalogoEliminar->value);

    Route::middleware('permission:'.Permission::PedidosVer->value)
        ->group(function () {

            Route::get(
                'pedidos/{order}/disenos',
                [DesignFileController::class, 'index']
            )->name('disenos.index');

            Route::post(
                'pedidos/{order}/disenos',
                [DesignFileController::class, 'store']
            )->name('disenos.store');

            Route::get(
                'pedidos/{order}/disenos/{designFile}/download',
                [DesignFileController::class, 'download']
            )->name('disenos.download');

            Route::post(
                'pedidos/{order}/disenos/{designFile}/approve',
                [DesignFileController::class, 'approve']
            )->name('disenos.approve');

            Route::delete(
                'pedidos/{order}/disenos/{designFile}',
                [DesignFileController::class, 'destroy']
            )->name('disenos.destroy');

        });

    Route::middleware('permission:'.Permission::PagosVer->value)
        ->group(function () {

            Route::get(
                'pagos',
                [PaymentController::class, 'globalIndex']
            )->name('pagos.index');

            Route::resource(
                'pedidos.pagos',
                PaymentController::class
            )->only(['index', 'store', 'destroy']);

            Route::post(
                'pedidos/{order}/pagos/{payment}/confirm',
                [PaymentController::class, 'confirm']
            )->name('pagos.confirm');

            Route::post(
                'pedidos/{order}/pagos/{payment}/reject',
                [PaymentController::class, 'reject']
            )->name('pagos.reject');

        });

    Route::middleware('permission:'.Permission::ReportesVer->value)
        ->group(function () {

            Route::get(
                'reportes',
                [ReportController::class, 'index']
            )->name('reportes.index');

            Route::get(
                'reportes/ingresos',
                [ReportController::class, 'income']
            )->name('reportes.ingresos')
                ->middleware('permission:'.Permission::ReportesFinancieros->value);

            Route::get(
                'reportes/servicios',
                [ReportController::class, 'services']
            )->name('reportes.servicios');

            Route::get(
                'reportes/clientes',
                [ReportController::class, 'clients']
            )->name('reportes.clientes');

            Route::get(
                'reportes/disenadores',
                [ReportController::class, 'designers']
            )->name('reportes.disenadores');

            Route::get(
                'reportes/morosos',
                [ReportController::class, 'morosos']
            )->name('reportes.morosos');

            Route::get(
                'reportes/export',
                [ReportController::class, 'export']
            )->name('reportes.export');

        });

    Route::get(
        'configuracion',
        [SettingsController::class, 'index']
    )->name('configuracion.index')
        ->middleware('permission:'.Permission::ConfiguracionVer->value);

    Route::post(
        'configuracion',
        [SettingsController::class, 'update']
    )->name('configuracion.update')
        ->middleware('permission:'.Permission::ConfiguracionGestionar->value);

    Route::delete(
        'configuracion/logo',
        [SettingsController::class, 'destroyLogo']
    )->name('configuracion.logo.destroy')
        ->middleware('permission:'.Permission::ConfiguracionGestionar->value);

    Route::middleware('permission:'.Permission::UsuariosGestionar->value)
        ->group(function () {

            Route::resource(
                'usuarios',
                UsersController::class
            )->parameters(['usuarios' => 'user'])->except(['show']);

            Route::post(
                'usuarios/{user}/toggle',
                [UsersController::class, 'toggleActive']
            )->name('usuarios.toggleActive');

            Route::resource(
                'roles',
                RoleController::class
            );

        });

    Route::middleware('permission:'.Permission::WebVer->value)
        ->group(function () {

            Route::get(
                'admin-web',
                [CarouselController::class, 'index']
            )->name('admin-web.carousel.index');

            Route::post(
                'admin-web/carousel',
                [CarouselController::class, 'store']
            )->name('admin-web.carousel.store');

            Route::put(
                'admin-web/carousel/{carouselSlide}',
                [CarouselController::class, 'update']
            )->name('admin-web.carousel.update');

            Route::delete(
                'admin-web/carousel/{carouselSlide}',
                [CarouselController::class, 'destroy']
            )->name('admin-web.carousel.destroy');

            Route::post(
                'admin-web/carousel/toggle/{carouselSlide}',
                [CarouselController::class, 'toggle']
            )->name('admin-web.carousel.toggle');

            Route::post(
                'admin-web/carousel/reorder',
                [CarouselController::class, 'reorder']
            )->name('admin-web.carousel.reorder');

            Route::get(
                'admin-web/configuracion',
                [WebSettingsController::class, 'index']
            )->name('admin-web.settings.index');

            Route::post(
                'admin-web/configuracion',
                [WebSettingsController::class, 'update']
            )->name('admin-web.settings.update');

            Route::delete(
                'admin-web/configuracion/logo',
                [WebSettingsController::class, 'destroyLogo']
            )->name('admin-web.settings.logo.destroy');

            Route::resource(
                'admin-web/media',
                MediaController::class
            )->only([
                'index',
                'store',
                'destroy',
            ]);

        });

});

require __DIR__.'/settings.php';
