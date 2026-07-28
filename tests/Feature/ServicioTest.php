<?php

use App\Models\Role;
use App\Models\Servicio;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

function adminUser()
{
    $user = User::factory()->create();
    $role = Role::where('nombre', 'administrador')->first();
    $user->roles()->sync([$role->id]);

    return $user->fresh();
}

test('authenticated team members can view service forms', function () {
    $user = adminUser();
    $team = $user->currentTeam;
    $servicio = Servicio::create([
        'nombre' => 'Fotografia',
        'descripcion' => 'Servicio de fotos',
        'precio' => '50.00',
        'estado' => 'activo',
    ]);

    $this
        ->actingAs($user)
        ->get(route('servicios.create', ['current_team' => $team]))
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(route('servicios.edit', ['current_team' => $team, 'servicio' => $servicio]))
        ->assertOk();
});

test('authenticated team members can create services', function () {
    $user = adminUser();
    $team = $user->currentTeam;

    $response = $this
        ->actingAs($user)
        ->post(route('servicios.store', ['current_team' => $team]), [
            'nombre' => 'Desarrollo web',
            'descripcion' => 'Servicio de desarrollo web',
            'precio' => '150.00',
            'estado' => 'activo',
        ]);

    $response->assertRedirect(route('servicios.index', ['current_team' => $team]));

    $this->assertDatabaseHas('servicios', [
        'nombre' => 'Desarrollo web',
        'descripcion' => 'Servicio de desarrollo web',
        'precio' => '150.00',
        'estado' => 'activo',
    ]);
});

test('authenticated team members can update services', function () {
    $user = adminUser();
    $team = $user->currentTeam;
    $servicio = Servicio::create([
        'nombre' => 'Original',
        'descripcion' => 'Descripcion original',
        'precio' => '100.00',
        'estado' => 'activo',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('servicios.update', ['current_team' => $team, 'servicio' => $servicio]), [
            'nombre' => 'Actualizado',
            'descripcion' => 'Descripcion actualizada',
            'precio' => '200.00',
            'estado' => 'inactivo',
        ]);

    $response->assertRedirect(route('servicios.index', ['current_team' => $team]));

    $this->assertDatabaseHas('servicios', [
        'id' => $servicio->id,
        'nombre' => 'Actualizado',
        'descripcion' => 'Descripcion actualizada',
        'precio' => '200.00',
        'estado' => 'inactivo',
    ]);
});

test('authenticated team members can delete services', function () {
    $user = adminUser();
    $team = $user->currentTeam;
    $servicio = Servicio::create([
        'nombre' => 'Servicio temporal',
        'descripcion' => 'Para eliminar',
        'precio' => '75.00',
        'estado' => 'activo',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('servicios.destroy', ['current_team' => $team, 'servicio' => $servicio]));

    $response->assertRedirect(route('servicios.index', ['current_team' => $team]));

    $this->assertDatabaseMissing('servicios', [
        'id' => $servicio->id,
    ]);
});
