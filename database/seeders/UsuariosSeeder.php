<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'email' => 'admin@diseno.com',
                'name' => 'Administrador',
                'phone' => '70000001',
                'role' => Role::ADMINISTRADOR,
            ],
            [
                'email' => 'diseno@diseno.com',
                'name' => 'Diseñador',
                'phone' => '70000002',
                'role' => Role::DISENADOR,
            ],
            [
                'email' => 'recepcion@diseno.com',
                'name' => 'Recepcionista',
                'phone' => '70000003',
                'role' => Role::RECEPCIONISTA,
            ],
        ];

        foreach ($usuarios as $datos) {
            $user = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'phone' => $datos['phone'],
                    'password' => '12345678',
                    'status' => UserStatus::Activo,
                    'email_verified_at' => now(),
                ],
            );

            $user->assignRole($datos['role']);
        }
    }
}
