<?php

namespace App\Enums;

enum Permission: string
{
    case PanelVer = 'panel.ver';
    case ClientesVer = 'clientes.ver';
    case ClientesGestionar = 'clientes.gestionar';
    case ServiciosVer = 'servicios.ver';
    case ServiciosGestionar = 'servicios.gestionar';
    case ProyectosVer = 'proyectos.ver';
    case ProyectosGestionar = 'proyectos.gestionar';
    case CotizacionesVer = 'cotizaciones.ver';
    case CotizacionesGestionar = 'cotizaciones.gestionar';
    case DiseñosVer = 'diseños.ver';
    case DiseñosGestionar = 'diseños.gestionar';
    case DiseñosAprobar = 'diseños.aprobar';
    case PedidosVer = 'pedidos.ver';
    case PedidosGestionar = 'pedidos.gestionar';
    case PagosVer = 'pagos.ver';
    case PagosGestionar = 'pagos.gestionar';
    case EntregasVer = 'entregas.ver';
    case EntregasGestionar = 'entregas.gestionar';
    case ReportesVer = 'reportes.ver';
    case ReportesFinancieros = 'reportes.financieros';
    case UsuariosGestionar = 'usuarios.gestionar';
    case ConfiguracionVer = 'configuracion.ver';
    case ConfiguracionGestionar = 'configuracion.gestionar';

    public function label(): string
    {
        return match ($this) {
            self::PanelVer => 'Ver Panel',
            self::ClientesVer => 'Listar clientes',
            self::ClientesGestionar => 'Crear/editar/eliminar clientes',
            self::ServiciosVer => 'Listar servicios',
            self::ServiciosGestionar => 'Crear/editar/eliminar servicios',
            self::ProyectosVer => 'Listar proyectos',
            self::ProyectosGestionar => 'Crear/editar/eliminar proyectos',
            self::CotizacionesVer => 'Listar cotizaciones',
            self::CotizacionesGestionar => 'Crear/editar/eliminar cotizaciones',
            self::DiseñosVer => 'Ver diseños',
            self::DiseñosGestionar => 'Subir diseños',
            self::DiseñosAprobar => 'Aprobar/rechazar diseños',
            self::PedidosVer => 'Listar pedidos',
            self::PedidosGestionar => 'Crear/editar/eliminar pedidos',
            self::PagosVer => 'Ver pagos',
            self::PagosGestionar => 'Registrar/confirmar pagos',
            self::EntregasVer => 'Ver entregas',
            self::EntregasGestionar => 'Gestionar entregas',
            self::ReportesVer => 'Ver reportes',
            self::ReportesFinancieros => 'Reportes financieros',
            self::UsuariosGestionar => 'Gestionar usuarios del sistema',
            self::ConfiguracionVer => 'Ver configuración',
            self::ConfiguracionGestionar => 'Editar configuración',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::PanelVer => 'panel',
            self::ClientesVer, self::ClientesGestionar => 'clientes',
            self::ServiciosVer, self::ServiciosGestionar => 'servicios',
            self::ProyectosVer, self::ProyectosGestionar => 'proyectos',
            self::CotizacionesVer, self::CotizacionesGestionar => 'cotizaciones',
            self::DiseñosVer, self::DiseñosGestionar, self::DiseñosAprobar => 'diseños',
            self::PedidosVer, self::PedidosGestionar => 'pedidos',
            self::PagosVer, self::PagosGestionar => 'pagos',
            self::EntregasVer, self::EntregasGestionar => 'entregas',
            self::ReportesVer, self::ReportesFinancieros => 'reportes',
            self::UsuariosGestionar => 'usuarios',
            self::ConfiguracionVer, self::ConfiguracionGestionar => 'configuracion',
        };
    }

    public static function forAdministrador(): array
    {
        return array_map(
            fn (self $case) => $case->value,
            self::cases()
        );
    }

    public static function forDisenador(): array
    {
        return [
            self::PanelVer->value,
            self::ClientesVer->value,
            self::ClientesGestionar->value,
            self::ServiciosVer->value,
            self::ServiciosGestionar->value,
            self::PedidosVer->value,
            self::DiseñosVer->value,
            self::DiseñosGestionar->value,
            self::ReportesVer->value,
        ];
    }

    public static function forAsistente(): array
    {
        return [
            self::PanelVer->value,
            self::ClientesVer->value,
            self::ClientesGestionar->value,
            self::ServiciosVer->value,
            self::ServiciosGestionar->value,
            self::PedidosVer->value,
            self::PedidosGestionar->value,
            self::PagosVer->value,
            self::PagosGestionar->value,
            self::EntregasVer->value,
            self::EntregasGestionar->value,
            self::ReportesVer->value,
        ];
    }

    public static function valuesForAdministrador(): array
    {
        return self::forAdministrador();
    }

    public static function valuesForDisenador(): array
    {
        return self::forDisenador();
    }

    public static function valuesForAsistente(): array
    {
        return self::forAsistente();
    }

    public static function forRecepcionista(): array
    {
        return self::forAsistente();
    }

    public static function valuesForRecepcionista(): array
    {
        return self::forRecepcionista();
    }
}
