<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email?} {--name=Administrador} {--password=12345678}';

    protected $description = 'Create or update an administrator user';

    public function handle(): void
    {
        $email = $this->argument('email') ?? $this->ask('Email del administrador');
        $name = $this->option('name');
        $password = $this->option('password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'status' => UserStatus::Activo,
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole(Role::ADMINISTRADOR);

        $this->info("Administrador '{$name}' listo.");
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
    }
}
