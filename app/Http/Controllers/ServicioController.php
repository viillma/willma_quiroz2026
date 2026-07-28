<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioFormRequest;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function index(): Response
    {
        $servicios = Servicio::orderBy('created_at', 'desc')->get();

        return Inertia::render('servicios/Index', [
            'servicios' => $servicios,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('servicios/Create');
    }

    public function store(ServicioFormRequest $request, string $currentTeam): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        Servicio::create($data);

        return to_route('servicios.index', [
            'current_team' => $currentTeam,
        ]);
    }

    public function edit(string $currentTeam, Servicio $servicio): Response
    {
        return Inertia::render('servicios/Edit', [
            'servicio' => $servicio,
        ]);
    }

    public function update(ServicioFormRequest $request, string $currentTeam, Servicio $servicio): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio->update($data);

        return to_route('servicios.index', [
            'current_team' => $currentTeam,
        ]);
    }

    public function destroy(string $currentTeam, Servicio $servicio): RedirectResponse
    {
        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }

        $servicio->delete();

        return to_route('servicios.index', [
            'current_team' => $currentTeam,
        ]);
    }
}
