<?php

namespace Database\Seeders;

use App\Actions\Teams\CreateTeam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $createTeam = app(CreateTeam::class);

        $admin = User::updateOrCreate(
            ['email' => 'admin@diseno.com'],
            [
                'name' => 'Administrador',
                'password' => '12345678',
                'role' => Role::ADMINISTRADOR,
            ]
        );
        $admin->assignRole(Role::ADMINISTRADOR);
        $this->ensureTeam($admin, $createTeam);

        $disenador = User::updateOrCreate(
            ['email' => 'diseno@diseno.com'],
            [
                'name' => 'Diseñador',
                'password' => '12345678',
                'role' => Role::DISENADOR,
            ]
        );
        $disenador->assignRole(Role::DISENADOR);
        $this->ensureTeam($disenador, $createTeam);

        $recepcionista = User::updateOrCreate(
            ['email' => 'recepcion@diseno.com'],
            [
                'name' => 'Recepcionista',
                'password' => '12345678',
                'role' => Role::RECEPCIONISTA,
            ]
        );
        $recepcionista->assignRole(Role::RECEPCIONISTA);
        $this->ensureTeam($recepcionista, $createTeam);

        $asistente = User::updateOrCreate(
            ['email' => 'asistente@diseno.com'],
            [
                'name' => 'Asistente',
                'password' => '12345678',
                'role' => Role::ASISTENTE,
            ]
        );
        $asistente->assignRole(Role::ASISTENTE);
        $this->ensureTeam($asistente, $createTeam);
    }

    private function ensureTeam(User $user, CreateTeam $createTeam): void
    {
        if (! $user->personalTeam()) {
            $team = $createTeam->handle($user, $user->name."'s Team", isPersonal: true);
            $user->switchTeam($team);
        }
    }
}
