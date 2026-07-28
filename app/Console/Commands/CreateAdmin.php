<?php

namespace App\Console\Commands;

use App\Actions\Teams\CreateTeam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email?} {--name=Administrador} {--password=12345678}';

    protected $description = 'Create a new admin user with a personal team';

    public function handle(CreateTeam $createTeam): void
    {
        $email = $this->argument('email') ?? $this->ask('Email del administrador');
        $name = $this->option('name');
        $password = $this->option('password');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = bcrypt($password);
            $user->name = $name;
            $user->role = Role::ADMINISTRADOR;
            $user->save();

            $user->roles()->sync(Role::where('nombre', Role::ADMINISTRADOR)->first()->id);
            $user->ensureRolesHavePermissions();

            $team = $user->currentTeam ?? $user->personalTeam();
            if (! $team) {
                $team = $createTeam->handle($user, "{$name}'s Team", isPersonal: true);
                $this->line("Equipo personal creado: {$team->name}");
            }

            $this->info("Administrador '{$name}' actualizado correctamente.");
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => Role::ADMINISTRADOR,
            ]);

            $createTeam->handle($user, "{$name}'s Team", isPersonal: true);

            $user->assignRole(Role::ADMINISTRADOR);
            $user->ensureRolesHavePermissions();

            $this->info("Administrador '{$name}' creado correctamente.");
        }

        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
    }
}
