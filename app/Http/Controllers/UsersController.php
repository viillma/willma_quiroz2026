<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(): Response
    {
        $users = User::with('role')->orderBy('created_at', 'desc')->get();

        return Inertia::render('usuarios/Index', [
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified_at,
                'status' => $user->status?->value,
                'role' => $user->role?->nombre,
                'created_at' => $user->created_at,
            ]),
            'roles' => Role::orderBy('nombre')->get(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('usuarios/Create', [
            'roles' => Role::orderBy('nombre')->get(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,nombre'],
            'status' => ['nullable', Rule::in(UserStatus::values())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'status' => $validated['status'] ?? UserStatus::Activo,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('usuarios.index');
    }

    public function edit(User $user): Response
    {
        $user->load('role');

        return Inertia::render('usuarios/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status?->value,
                'role' => $user->role?->nombre,
            ],
            'roles' => Role::orderBy('nombre')->get(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'exists:roles,nombre'],
            'status' => ['required', Rule::in(UserStatus::values())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->isAdmin() && $validated['role'] !== Role::ADMINISTRADOR && $this->isLastAdmin($user)) {
            return back()->withErrors(['role' => 'No se puede cambiar el rol del único administrador del sistema.']);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        $user->assignRole($validated['role']);

        return redirect()->route('usuarios.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($this->isLastAdmin($user)) {
            return back()->withErrors(['error' => 'No se puede eliminar el único administrador del sistema.']);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        $user->delete();

        return redirect()->route('usuarios.index');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes cambiar tu propio estado.']);
        }

        $user->update([
            'status' => $user->isActive() ? UserStatus::Suspendido : UserStatus::Activo,
        ]);

        return redirect()->route('usuarios.index');
    }

    private function isLastAdmin(User $user): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        return User::whereHas('role', fn ($query) => $query->where('nombre', Role::ADMINISTRADOR))
            ->count() <= 1;
    }
}
