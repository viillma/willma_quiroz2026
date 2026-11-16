<?php

namespace App\Console\Commands;

use App\Enums\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class SyncRoles extends Command
{
    protected $signature = 'app:sync-roles';

    protected $description = 'Sync the default roles and their permissions';

    public function handle(): void
    {
        foreach (Permission::byRole() as $roleName => $permissions) {
            $role = Role::where('nombre', $roleName)->first();

            if (! $role) {
                $this->warn("Rol '{$roleName}' no encontrado, se omite.");

                continue;
            }

            $role->syncPermissionsByName($permissions);

            $this->line("Rol '{$roleName}' sincronizado (".count($permissions).' permisos).');
        }

        $this->info('Roles y permisos sincronizados correctamente.');
    }
}
