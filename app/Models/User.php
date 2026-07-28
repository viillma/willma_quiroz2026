<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use App\Enums\Permission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'current_team_id', 'role', 'activo'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;

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
            'activo' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::where('nombre', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('nombre', $role)->exists();
    }

    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles()->whereIn('nombre', $roles)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMINISTRADOR);
    }

    public function rolesNombres(): array
    {
        return $this->roles()->pluck('nombre')->toArray();
    }

    public function permissionsNombres(): array
    {
        if ($this->isAdmin()) {
            return Permission::valuesForAdministrador();
        }

        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions.*.nombre')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissionsNombres());
    }

    public function canAccessPanel(): bool
    {
        return $this->hasPermission(Permission::PanelVer->value);
    }

    public function syncRolesFromLegacyColumn(): void
    {
        if ($this->role && ! $this->roles()->exists()) {
            $this->assignRole($this->role);
        }
    }

    public function ensureRolesHavePermissions(): void
    {
        foreach ($this->roles()->get() as $role) {
            $role->ensureDefaultPermissions();
        }
    }
}
