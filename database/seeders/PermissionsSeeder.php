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

        $disenadorRole = Role::updateOrCreate(
            ['nombre' => Role::DISENADOR],
            ['descripcion' => 'Diseñador con permisos de diseño y proyectos']
        );

        $asistenteRole = Role::updateOrCreate(
            ['nombre' => Role::ASISTENTE],
            ['descripcion' => 'Asistente con permisos de operación diaria']
        );

        $recepcionistaRole = Role::updateOrCreate(
            ['nombre' => Role::RECEPCIONISTA],
            ['descripcion' => 'Recepcionista encargado de atención al cliente y operación diaria']
        );

        $adminRole->syncPermissionsByName(Permission::valuesForAdministrador());
        $disenadorRole->syncPermissionsByName(Permission::valuesForDisenador());
        $asistenteRole->syncPermissionsByName(Permission::valuesForAsistente());
        $recepcionistaRole->syncPermissionsByName(Permission::valuesForRecepcionista());
    }
}
