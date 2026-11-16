<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'phone', 'password', 'role_id', 'status'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'status' => UserStatus::class,
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Assign a single role by its name, replacing the current one.
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('nombre', $roleName)->first() ?? Role::defaultRole($roleName);

        $this->forceFill(['role_id' => $role->id])->save();
        $this->setRelation('role', $role);
    }

    public function hasRole(string $role): bool
    {
        return strtolower((string) $this->role?->nombre) === strtolower($role);
    }

    /**
     * @param  array<int, string>|string  $roles
     */
    public function hasAnyRole(array|string $roles): bool
    {
        $roles = array_map('strtolower', is_array($roles) ? $roles : [$roles]);

        return $this->role !== null && in_array(strtolower($this->role->nombre), $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMINISTRADOR);
    }

    /**
     * The role names held by the user (single-role model).
     *
     * @return array<int, string>
     */
    public function rolesNombres(): array
    {
        return $this->role ? [$this->role->nombre] : [];
    }

    /**
     * @return array<int, string>
     */
    public function permissionsNombres(): array
    {
        if ($this->isAdmin()) {
            return Permission::valuesForAdministrador();
        }

        if (! $this->role) {
            return [];
        }

        return $this->role->permissions()
            ->pluck('nombre')
            ->unique()
            ->values()
            ->all();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissionsNombres(), true);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Activo;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::Pendiente;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspendido;
    }

    public function canAccessPanel(): bool
    {
        return $this->isActive() && $this->hasPermission(Permission::PanelVer->value);
    }

    /**
     * The named route the user should land on after authenticating.
     */
    public function landingRoute(): string
    {
        return match (true) {
            $this->isAdmin() => 'admin',
            $this->hasRole(Role::RECEPCIONISTA) => 'recepcion',
            $this->hasRole(Role::DISENADOR) => 'diseno',
            default => 'dashboard',
        };
    }
}
