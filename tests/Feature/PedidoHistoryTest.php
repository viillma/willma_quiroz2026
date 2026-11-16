<?php

use App\Models\Client;
use App\Models\Order;
use App\Models\Servicio;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);

    $this->user = User::factory()->admin()->create();

    $this->client = Client::create(['name' => 'Cliente Test']);
    $this->servicio = Servicio::create(['nombre' => 'Diseño Test', 'precio' => 100]);
});

test('creating an order records an initial history entry', function () {
    $this->actingAs($this->user)
        ->post(route('pedidos.store'), [
            'client_id' => $this->client->id,
            'status' => 'nuevo',
            'servicios' => [
                ['servicio_id' => $this->servicio->id, 'quantity' => 2, 'unit_price' => 100],
            ],
        ])
        ->assertRedirect();

    $order = Order::firstOrFail();

    expect($order->histories)->toHaveCount(1);
    expect($order->histories->first()->from_status)->toBeNull();
    expect($order->histories->first()->to_status)->toBe('nuevo');
    expect((float) $order->total)->toBe(200.0);
});

test('updating an order status records a history entry', function () {
    $order = Order::create([
        'code' => Order::generateCode(),
        'client_id' => $this->client->id,
        'status' => 'nuevo',
        'total' => 100,
    ]);

    $this->actingAs($this->user)
        ->put(route('pedidos.update', ['pedido' => $order]), [
            'client_id' => $this->client->id,
            'status' => 'en_proceso',
            'servicios' => [
                ['servicio_id' => $this->servicio->id, 'quantity' => 1, 'unit_price' => 100],
            ],
        ])
        ->assertRedirect();

    $history = $order->fresh()->histories->first();

    expect($order->fresh()->histories)->toHaveCount(1);
    expect($history->from_status)->toBe('nuevo');
    expect($history->to_status)->toBe('en_proceso');
});

test('updating an order without status changes does not record a history entry', function () {
    $order = Order::create([
        'code' => Order::generateCode(),
        'client_id' => $this->client->id,
        'status' => 'nuevo',
        'total' => 100,
    ]);

    $this->actingAs($this->user)
        ->put(route('pedidos.update', ['pedido' => $order]), [
            'client_id' => $this->client->id,
            'status' => 'nuevo',
            'notes' => 'Solo cambia una nota',
            'servicios' => [
                ['servicio_id' => $this->servicio->id, 'quantity' => 1, 'unit_price' => 100],
            ],
        ])
        ->assertRedirect();

    expect($order->fresh()->histories)->toHaveCount(0);
});
