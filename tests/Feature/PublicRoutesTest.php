<?php

use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

test('public marketing pages keep their flat urls', function () {
    $this->get('/servicios')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('public/Servicios'));

    $this->get('/catalogo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('public/Catalogo'));

    $this->get('/portafolio')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('public/Portafolio'));
});

test('admin panel routes are prefixed with panel', function () {
    expect(route('dashboard', absolute: false))->toBe('/panel/dashboard');
    expect(route('servicios.index', absolute: false))->toBe('/panel/servicios');
    expect(route('servicios.create', absolute: false))->toBe('/panel/servicios/create');
    expect(route('catalogo.index', absolute: false))->toBe('/panel/catalogo');
    expect(route('portafolio.index', absolute: false))->toBe('/panel/portafolio');
});

test('admin panel pages are not shadowed by the public routes', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->get(route('servicios.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('servicios/Index'));

    $this->actingAs($user)->get(route('servicios.create'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('servicios/Create'));

    $this->actingAs($user)->get(route('catalogo.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('catalog/Admin'));

    $this->actingAs($user)->get(route('portafolio.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('portafolio/Index'));
});
