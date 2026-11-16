<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Models\Permission as PermissionModel;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Permission::cases() as $permission) {
            PermissionModel::updateOrCreate(
                ['nombre' => $permission->value],
                [
                    'descripcion' => $permission->label(),
                    'grupo' => $permission->group(),
                ]
            );
        }

        $adminRole = Role::updateOrCreate(
            ['nombre' => Role::ADMINISTRADOR],
            ['descripcion' => 'Administrador con acceso total al sistema']
        );

        $recepcionistaRole = Role::updateOrCreate(
            ['nombre' => Role::RECEPCIONISTA],
            ['descripcion' => 'Recepción: clientes, pedidos, cotizaciones y seguimiento']
        );

        $disenadorRole = Role::updateOrCreate(
            ['nombre' => Role::DISENADOR],
            ['descripcion' => 'Diseñador: pedidos asignados, diseños y archivos']
        );

        $adminRole->syncPermissionsByName(Permission::valuesForAdministrador());
        $recepcionistaRole->syncPermissionsByName(Permission::valuesForRecepcionista());
        $disenadorRole->syncPermissionsByName(Permission::valuesForDisenador());
    }
}
