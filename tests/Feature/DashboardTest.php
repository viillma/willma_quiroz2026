<?php

use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('active administrators can visit the dashboard', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('active recepcionistas can visit the dashboard', function () {
    $user = User::factory()->recepcionista()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('active disenadores can visit the dashboard', function () {
    $user = User::factory()->disenador()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('pending users are logged out and redirected to the login page', function () {
    $user = User::factory()->admin()->pending()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));

    $this->assertGuest();
});

test('suspended users are logged out and redirected to the login page', function () {
    $user = User::factory()->recepcionista()->suspended()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));

    $this->assertGuest();
});
