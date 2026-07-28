<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public const ADMINISTRADOR = 'administrador';

    public const DISENADOR = 'disenador';

    public const ASISTENTE = 'asistente';

    public const RECEPCIONISTA = 'recepcionista';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    public function syncPermissionsByName(array $names): void
    {
        $permissions = Permission::whereIn('nombre', $names)->get();
        $this->permissions()->sync($permissions);
    }

    public function ensureDefaultPermissions(): void
    {
        if ($this->permissions()->doesntExist()) {
            $permissions = match ($this->nombre) {
                self::ADMINISTRADOR => \App\Enums\Permission::valuesForAdministrador(),
                self::DISENADOR => \App\Enums\Permission::valuesForDisenador(),
                self::ASISTENTE => \App\Enums\Permission::valuesForAsistente(),
                self::RECEPCIONISTA => \App\Enums\Permission::valuesForRecepcionista(),
                default => [],
            };

            if (! empty($permissions)) {
                $this->syncPermissionsByName($permissions);
            }
        }
    }
}
