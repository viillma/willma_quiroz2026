<?php

use App\Actions\Fortify\CreateNewUser;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Validation\ValidationException;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

function registerUser(array $overrides = []): User
{
    return app(CreateNewUser::class)->create(array_merge([
        'name' => 'Nuevo Usuario',
        'email' => 'nuevo@example.com',
        'phone' => '70012345',
        'password' => 'password',
        'password_confirmation' => 'password',
        'tipo_usuario' => 'recepcionista',
    ], $overrides));
}

test('registration stores the phone number and starts as pending', function () {
    $user = registerUser();

    expect($user->phone)->toBe('70012345');
    expect($user->status)->toBe(UserStatus::Pendiente);
    expect($user->isPending())->toBeTrue();
});

test('registration defaults to the recepcionista role', function () {
    $user = registerUser();

    expect($user->role?->nombre)->toBe(Role::RECEPCIONISTA);
    expect($user->hasRole(Role::RECEPCIONISTA))->toBeTrue();
});

test('registration maps empleado to the disenador role', function () {
    $user = registerUser([
        'email' => 'empleado@example.com',
        'tipo_usuario' => 'empleado',
    ]);

    expect($user->role?->nombre)->toBe(Role::DISENADOR);
    expect($user->hasRole(Role::DISENADOR))->toBeTrue();
});

test('registration rejects the administrador role', function () {
    registerUser([
        'email' => 'admin@example.com',
        'tipo_usuario' => 'administrador',
    ]);
})->throws(ValidationException::class);

test('registration rejects an invalid tipo_usuario', function () {
    registerUser([
        'email' => 'invalido@example.com',
        'tipo_usuario' => 'superadmin',
    ]);
})->throws(ValidationException::class);
