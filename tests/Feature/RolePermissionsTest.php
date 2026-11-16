<?php

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

function userWithRole(string $roleName): User
{
    $role = Role::where('nombre', $roleName)->firstOrFail();

    return User::factory()->create(['role_id' => $role->id]);
}

test('administrador can access the dashboard', function () {
    $user = userWithRole(Role::ADMINISTRADOR);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('administrador is identified as admin', function () {
    $user = userWithRole(Role::ADMINISTRADOR);

    expect($user->isAdmin())->toBeTrue();
});

test('administrador has all permissions', function () {
    $user = userWithRole(Role::ADMINISTRADOR);

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::CatalogoEliminar->value))->toBeTrue();
    expect($user->hasPermission(Permission::WebGestionar->value))->toBeTrue();
});

test('recepcionista can access the dashboard', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('recepcionista has front desk permissions', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::PedidosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::PedidosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::CotizacionesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::CotizacionesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::PagosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::CatalogoVer->value))->toBeTrue();
});

test('recepcionista lacks advanced permissions', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    expect($user->hasPermission(Permission::ServiciosGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::DiseñosVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::ReportesVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::ReportesFinancieros->value))->toBeFalse();
    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeFalse();
});

test('recepcionista cannot manage users', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
});

test('recepcionista cannot access reports', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    $this->actingAs($user)->get(route('reportes.index'))->assertForbidden();
});

test('disenador has design permissions', function () {
    $user = userWithRole(Role::DISENADOR);

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::PedidosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::DiseñosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::DiseñosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::DiseñosAprobar->value))->toBeTrue();
    expect($user->hasPermission(Permission::CatalogoVer->value))->toBeTrue();
});

test('disenador cannot manage users, money or configuration', function () {
    $user = userWithRole(Role::DISENADOR);

    expect($user->hasPermission(Permission::ClientesGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::PedidosGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::PagosVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::ReportesVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeFalse();
});

test('disenador can access the dashboard', function () {
    $user = userWithRole(Role::DISENADOR);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('rolesNombres returns the assigned role', function () {
    $user = userWithRole(Role::ADMINISTRADOR);

    expect($user->rolesNombres())->toBe([Role::ADMINISTRADOR]);
});

test('permissionsNombres returns all permissions for administrador', function () {
    $user = userWithRole(Role::ADMINISTRADOR);
    $permissions = $user->permissionsNombres();

    expect($permissions)->toContain(Permission::ClientesGestionar->value);
    expect($permissions)->toContain(Permission::ServiciosGestionar->value);
    expect($permissions)->toContain(Permission::UsuariosGestionar->value);
});

test('permissionsNombres returns limited permissions for recepcionista', function () {
    $user = userWithRole(Role::RECEPCIONISTA);
    $permissions = $user->permissionsNombres();

    expect($permissions)->toContain(Permission::ClientesGestionar->value);
    expect($permissions)->toContain(Permission::PedidosGestionar->value);
    expect($permissions)->not->toContain(Permission::UsuariosGestionar->value);
});

test('assignRole replaces the current role', function () {
    $user = userWithRole(Role::RECEPCIONISTA);

    $user->assignRole(Role::DISENADOR);

    expect($user->fresh()->role?->nombre)->toBe(Role::DISENADOR);
    expect($user->fresh()->hasRole(Role::DISENADOR))->toBeTrue();
});
