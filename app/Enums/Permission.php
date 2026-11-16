<?php

namespace App\Enums;

enum Permission: string
{
    case PanelVer = 'panel.ver';
    case ClientesVer = 'clientes.ver';
    case ClientesGestionar = 'clientes.gestionar';
    case ServiciosVer = 'servicios.ver';
    case ServiciosGestionar = 'servicios.gestionar';
    case CotizacionesVer = 'cotizaciones.ver';
    case CotizacionesGestionar = 'cotizaciones.gestionar';
    case DiseñosVer = 'diseños.ver';
    case DiseñosGestionar = 'diseños.gestionar';
    case DiseñosAprobar = 'diseños.aprobar';
    case PedidosVer = 'pedidos.ver';
    case PedidosGestionar = 'pedidos.gestionar';
    case PagosVer = 'pagos.ver';
    case PagosGestionar = 'pagos.gestionar';
    case ReportesVer = 'reportes.ver';
    case ReportesFinancieros = 'reportes.financieros';
    case UsuariosGestionar = 'usuarios.gestionar';
    case ConfiguracionVer = 'configuracion.ver';
    case ConfiguracionGestionar = 'configuracion.gestionar';
    case CatalogoVer = 'catalogo.ver';
    case CatalogoCrear = 'catalogo.crear';
    case CatalogoEditar = 'catalogo.editar';
    case CatalogoEliminar = 'catalogo.eliminar';
    case WebVer = 'web.ver';
    case WebGestionar = 'web.gestionar';

    public function label(): string
    {
        return match ($this) {
            self::PanelVer => 'Ver Panel',
            self::ClientesVer => 'Listar clientes',
            self::ClientesGestionar => 'Crear/editar/eliminar clientes',
            self::ServiciosVer => 'Listar servicios',
            self::ServiciosGestionar => 'Crear/editar/eliminar servicios',
            self::CotizacionesVer => 'Listar cotizaciones',
            self::CotizacionesGestionar => 'Crear/editar/eliminar cotizaciones',
            self::DiseñosVer => 'Ver diseños',
            self::DiseñosGestionar => 'Subir diseños',
            self::DiseñosAprobar => 'Aprobar/rechazar diseños',
            self::PedidosVer => 'Listar pedidos',
            self::PedidosGestionar => 'Crear/editar/eliminar pedidos',
            self::PagosVer => 'Ver pagos',
            self::PagosGestionar => 'Registrar/confirmar pagos',
            self::ReportesVer => 'Ver reportes',
            self::ReportesFinancieros => 'Reportes financieros',
            self::UsuariosGestionar => 'Gestionar usuarios del sistema',
            self::ConfiguracionVer => 'Ver configuración',
            self::ConfiguracionGestionar => 'Editar configuración',
            self::CatalogoVer => 'Visualizar catálogo',
            self::CatalogoCrear => 'Crear diseños en el catálogo',
            self::CatalogoEditar => 'Editar diseños del catálogo',
            self::CatalogoEliminar => 'Eliminar diseños del catálogo',
            self::WebVer => 'Ver administrador de la web',
            self::WebGestionar => 'Gestionar contenido de la web',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::PanelVer => 'panel',
            self::ClientesVer, self::ClientesGestionar => 'clientes',
            self::ServiciosVer, self::ServiciosGestionar => 'servicios',
            self::CotizacionesVer, self::CotizacionesGestionar => 'cotizaciones',
            self::DiseñosVer, self::DiseñosGestionar, self::DiseñosAprobar => 'diseños',
            self::PedidosVer, self::PedidosGestionar => 'pedidos',
            self::PagosVer, self::PagosGestionar => 'pagos',
            self::ReportesVer, self::ReportesFinancieros => 'reportes',
            self::UsuariosGestionar => 'usuarios',
            self::ConfiguracionVer, self::ConfiguracionGestionar => 'configuracion',
            self::CatalogoVer, self::CatalogoCrear, self::CatalogoEditar, self::CatalogoEliminar => 'catalogo',
            self::WebVer, self::WebGestionar => 'web',
        };
    }

    /**
     * Every permission in the system.
     *
     * @return array<int, string>
     */
    public static function valuesForAdministrador(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Front desk: customers, orders, quotations and payment tracking.
     *
     * @return array<int, string>
     */
    public static function valuesForRecepcionista(): array
    {
        return [
            self::PanelVer->value,
            self::ClientesVer->value,
            self::ClientesGestionar->value,
            self::ServiciosVer->value,
            self::PedidosVer->value,
            self::PedidosGestionar->value,
            self::CotizacionesVer->value,
            self::CotizacionesGestionar->value,
            self::PagosVer->value,
            self::PagosGestionar->value,
            self::CatalogoVer->value,
        ];
    }

    /**
     * Designers: assigned orders, design files and approvals.
     *
     * @return array<int, string>
     */
    public static function valuesForDisenador(): array
    {
        return [
            self::PanelVer->value,
            self::ClientesVer->value,
            self::ServiciosVer->value,
            self::PedidosVer->value,
            self::DiseñosVer->value,
            self::DiseñosGestionar->value,
            self::DiseñosAprobar->value,
            self::CatalogoVer->value,
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function byRole(): array
    {
        return [
            'administrador' => self::valuesForAdministrador(),
            'recepcionista' => self::valuesForRecepcionista(),
            'disenador' => self::valuesForDisenador(),
        ];
    }
}
