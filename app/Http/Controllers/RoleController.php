<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::withCount('users')->orderBy('created_at', 'desc')->get();

        return Inertia::render('roles/Index', [
            'roles' => $roles->map(fn ($role) => [
                'id' => $role->id,
                'nombre' => $role->nombre,
                'descripcion' => $role->descripcion,
                'users_count' => $role->users_count,
                'created_at' => $role->created_at,
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('roles/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique(Role::class)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $role = Role::create($validated);

        $role->ensureDefaultPermissions();

        return redirect()->route('roles.index');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('roles/Edit', [
            'role' => [
                'id' => $role->id,
                'nombre' => $role->nombre,
                'descripcion' => $role->descripcion,
            ],
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique(Role::class)->ignore($role->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update($validated);

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (strtolower($role->nombre) === Role::ADMINISTRADOR) {
            return back()->withErrors(['error' => 'No se puede eliminar el rol de administrador.']);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar un rol con usuarios asignados.']);
        }

        $role->delete();

        return redirect()->route('roles.index');
    }
}
