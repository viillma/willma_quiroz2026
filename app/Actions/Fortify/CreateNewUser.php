<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * Self-registration is limited to operational roles. Administrator
     * accounts can only be created from within the panel by an admin.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => $this->passwordRules(),
            'tipo_usuario' => ['required', Rule::in(['recepcionista', 'disenador', 'empleado'])],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => $input['password'],
            'status' => UserStatus::Pendiente,
        ]);

        $user->assignRole($this->roleFor($input['tipo_usuario']));

        return $user;
    }

    /**
     * Resolve the role assigned to a new user.
     */
    private function roleFor(string $tipo): string
    {
        return match ($tipo) {
            'disenador', 'empleado' => Role::DISENADOR,
            default => Role::RECEPCIONISTA,
        };
    }
}
