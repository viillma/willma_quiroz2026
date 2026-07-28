<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

test('guests are redirected to the login page', function () {
    $response = $this->get('/some-team/dashboard');
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->seed(PermissionsSeeder::class);

    $user = User::factory()->create();
    $role = Role::where('nombre', 'asistente')->first();
    $user->roles()->sync([$role->id]);
    $team = $user->currentTeam;

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard', ['current_team' => $team]));

    $response->assertOk();
});
