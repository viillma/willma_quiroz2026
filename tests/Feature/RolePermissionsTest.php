<?php

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

function userWithRole(string $roleName): User
{
    $user = User::factory()->create(['role' => $roleName]);
    $role = Role::where('nombre', $roleName)->firstOrFail();
    $user->roles()->sync([$role->id]);

    return $user->fresh();
}

test('administrador can access dashboard', function () {
    $user = userWithRole('administrador');
    $team = $user->currentTeam;

    $response = $this->actingAs($user)->get("/{$team->slug}/dashboard");

    $response->assertOk();
});

test('administrador is identified as admin', function () {
    $user = userWithRole('administrador');

    expect($user->isAdmin())->toBeTrue();
});

test('administrador has all permissions', function () {
    $user = userWithRole('administrador');

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ProyectosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeTrue();
});

test('asistente can access dashboard', function () {
    $user = userWithRole('asistente');
    $team = $user->currentTeam;

    $response = $this->actingAs($user)->get("/{$team->slug}/dashboard");

    $response->assertOk();
});

test('asistente can access servicios management', function () {
    $user = userWithRole('asistente');
    $team = $user->currentTeam;

    $response = $this->actingAs($user)->get("/{$team->slug}/servicios/create");

    $response->assertOk();
});

test('asistente cannot manage users', function () {
    $user = userWithRole('asistente');

    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeFalse();
});

test('asistente has basic permissions', function () {
    $user = userWithRole('asistente');

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::CotizacionesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::CotizacionesGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ReportesVer->value))->toBeTrue();
});

test('asistente lacks advanced permissions', function () {
    $user = userWithRole('asistente');

    expect($user->hasPermission(Permission::ProyectosVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::DiseñosVer->value))->toBeFalse();
    expect($user->hasPermission(Permission::ReportesFinancieros->value))->toBeFalse();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeFalse();
});

test('disenador has design permissions', function () {
    $user = userWithRole('disenador');

    expect($user->hasPermission(Permission::PanelVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ClientesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ServiciosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ProyectosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::ProyectosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::CotizacionesVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::DiseñosVer->value))->toBeTrue();
    expect($user->hasPermission(Permission::DiseñosGestionar->value))->toBeTrue();
    expect($user->hasPermission(Permission::ReportesVer->value))->toBeTrue();
});

test('disenador cannot manage users or configuration', function () {
    $user = userWithRole('disenador');

    expect($user->hasPermission(Permission::UsuariosGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::ConfiguracionGestionar->value))->toBeFalse();
    expect($user->hasPermission(Permission::ReportesFinancieros->value))->toBeFalse();
});

test('disenador can manage servicios', function () {
    $user = userWithRole('disenador');
    $team = $user->currentTeam;

    $response = $this->actingAs($user)->get("/{$team->slug}/servicios/create");

    $response->assertOk();
});

test('rolesNombres returns correct roles', function () {
    $user = userWithRole('administrador');

    expect($user->rolesNombres())->toContain('administrador');
});

test('permissionsNombres returns all permissions for administrador', function () {
    $user = userWithRole('administrador');
    $permissions = $user->permissionsNombres();

    expect($permissions)->toContain(Permission::ClientesGestionar->value);
    expect($permissions)->toContain(Permission::ServiciosGestionar->value);
    expect($permissions)->toContain(Permission::UsuariosGestionar->value);
});

test('permissionsNombres returns limited permissions for asistente', function () {
    $user = userWithRole('asistente');
    $permissions = $user->permissionsNombres();

    expect($permissions)->toContain(Permission::ClientesGestionar->value);
    expect($permissions)->toContain(Permission::ServiciosGestionar->value);
    expect($permissions)->not->toContain(Permission::UsuariosGestionar->value);
});
