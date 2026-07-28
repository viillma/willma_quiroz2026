<?php

namespace App\Http\Controllers;

use App\Actions\Teams\CreateTeam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function __construct(private CreateTeam $createTeam)
    {
    }

    public function index(): Response
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();

        return Inertia::render('usuarios/Index', [
            'users' => $users->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'activo' => $user->activo,
                'roles' => $user->roles->pluck('nombre'),
                'created_at' => $user->created_at,
            ]),
            'roles' => Role::all(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('usuarios/Create', [
            'roles' => Role::all(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function store(Request $request, string $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,nombre'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $user->assignRole($validated['role']);
        $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

        return redirect()->route('usuarios.index', ['current_team' => $currentTeam]);
    }

    public function edit(string $currentTeam, User $user): Response
    {
        $user->load('roles');

        return Inertia::render('usuarios/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'activo' => $user->activo,
                'roles' => $user->roles->pluck('nombre'),
            ],
            'roles' => Role::all(['id', 'nombre', 'descripcion']),
        ]);
    }

    public function update(Request $request, string $currentTeam, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', 'string', 'exists:roles,nombre'],
            'activo' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'activo' => $validated['activo'] ?? $user->activo,
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->roles()->detach();
        $user->assignRole($validated['role']);

        return redirect()->route('usuarios.index', ['current_team' => $currentTeam]);
    }

    public function destroy(string $currentTeam, User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::whereHas('roles', fn ($q) => $q->where('nombre', Role::ADMINISTRADOR))->count() <= 1) {
            return back()->withErrors(['error' => 'No se puede eliminar el único administrador del sistema.']);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        $user->delete();

        return redirect()->route('usuarios.index', ['current_team' => $currentTeam]);
    }

    public function toggleActive(string $currentTeam, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes desactivarte a ti mismo.']);
        }

        $user->update(['activo' => ! $user->activo]);

        return redirect()->route('usuarios.index', ['current_team' => $currentTeam]);
    }
}
