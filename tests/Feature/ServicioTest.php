<?php

use App\Models\Servicio;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

function adminUser(): User
{
    return User::factory()->admin()->create();
}

test('authorized users can view service forms', function () {
    $user = adminUser();
    $servicio = Servicio::create([
        'nombre' => 'Fotografia',
        'descripcion' => 'Servicio de fotos',
        'precio' => '50.00',
        'estado' => 'activo',
    ]);

    $this->actingAs($user)
        ->get(route('servicios.create'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('servicios.edit', ['servicio' => $servicio]))
        ->assertOk();
});

test('authorized users can create services', function () {
    $user = adminUser();

    $response = $this
        ->actingAs($user)
        ->post(route('servicios.store'), [
            'nombre' => 'Desarrollo web',
            'descripcion' => 'Servicio de desarrollo web',
            'precio' => '150.00',
            'estado' => 'activo',
        ]);

    $response->assertRedirect(route('servicios.index'));

    $this->assertDatabaseHas('servicios', [
        'nombre' => 'Desarrollo web',
        'descripcion' => 'Servicio de desarrollo web',
        'precio' => '150.00',
        'estado' => 'activo',
    ]);
});

test('authorized users can update services', function () {
    $user = adminUser();
    $servicio = Servicio::create([
        'nombre' => 'Original',
        'descripcion' => 'Descripcion original',
        'precio' => '100.00',
        'estado' => 'activo',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('servicios.update', ['servicio' => $servicio]), [
            'nombre' => 'Actualizado',
            'descripcion' => 'Descripcion actualizada',
            'precio' => '200.00',
            'estado' => 'inactivo',
        ]);

    $response->assertRedirect(route('servicios.index'));

    $this->assertDatabaseHas('servicios', [
        'id' => $servicio->id,
        'nombre' => 'Actualizado',
        'descripcion' => 'Descripcion actualizada',
        'precio' => '200.00',
        'estado' => 'inactivo',
    ]);
});

test('authorized users can delete services', function () {
    $user = adminUser();
    $servicio = Servicio::create([
        'nombre' => 'Servicio temporal',
        'descripcion' => 'Para eliminar',
        'precio' => '75.00',
        'estado' => 'activo',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('servicios.destroy', ['servicio' => $servicio]));

    $response->assertRedirect(route('servicios.index'));

    $this->assertDatabaseMissing('servicios', [
        'id' => $servicio->id,
    ]);
});

test('recepcionistas cannot manage services', function () {
    $user = User::factory()->recepcionista()->create();

    $this->actingAs($user)
        ->get(route('servicios.create'))
        ->assertForbidden();
});
