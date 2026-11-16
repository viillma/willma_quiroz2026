<?php

namespace App\Models;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMINISTRADOR = 'administrador';

    public const RECEPCIONISTA = 'recepcionista';

    public const DISENADOR = 'disenador';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    /**
     * @param  array<int, string>  $names
     */
    public function syncPermissionsByName(array $names): void
    {
        $permissions = Permission::whereIn('nombre', $names)->get();

        $this->permissions()->sync($permissions);
    }

    public function ensureDefaultPermissions(): void
    {
        if ($this->permissions()->exists()) {
            return;
        }

        $permissions = match ($this->nombre) {
            self::ADMINISTRADOR => PermissionEnum::valuesForAdministrador(),
            self::RECEPCIONISTA => PermissionEnum::valuesForRecepcionista(),
            self::DISENADOR => PermissionEnum::valuesForDisenador(),
            default => [],
        };

        if ($permissions !== []) {
            $this->syncPermissionsByName($permissions);
        }
    }

    /**
     * Resolve (or create) a default system role and make sure it has permissions.
     */
    public static function defaultRole(string $name): self
    {
        $role = self::firstOrCreate(['nombre' => $name]);

        $role->ensureDefaultPermissions();

        return $role;
    }

    /**
     * The default system role names.
     *
     * @return array<int, string>
     */
    public static function defaults(): array
    {
        return [
            self::ADMINISTRADOR,
            self::RECEPCIONISTA,
            self::DISENADOR,
        ];
    }
}
