<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortafolioFormRequest;
use App\Models\Client;
use App\Models\Portafolio;
use App\Models\PortafolioImage;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PortafolioController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Portafolio::with(['client:id,name', 'servicio:id,nombre']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->string('categoria'));
        }

        if ($request->filled('cliente')) {
            $query->where('client_id', $request->integer('cliente'));
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->string('fecha'));
        }

        $portafolios = $query->orderByDesc('created_at')->get()->map(fn ($p) => $this->toArray($p));

        return Inertia::render('portafolio/Index', [
            'portafolios' => $portafolios,
            'categorias' => Portafolio::CATEGORIAS(),
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $request->input('search') ?? '',
                'categoria' => $request->input('categoria') ?? '',
                'cliente' => $request->input('cliente') ?? '',
                'fecha' => $request->input('fecha') ?? '',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('portafolio/Create', [
            'categorias' => Portafolio::CATEGORIAS(),
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre']),
        ]);
    }

    public function store(PortafolioFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $portafolio = DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('imagen_principal')) {
                $data['imagen_principal'] = $request->file('imagen_principal')->store('portafolio', 'public');
            }

            $portafolio = Portafolio::create($data);

            if ($request->hasFile('imagenes_adicionales')) {
                $orden = 1;
                foreach ($request->file('imagenes_adicionales') as $file) {
                    $path = $file->store('portafolio', 'public');
                    PortafolioImage::create([
                        'portafolio_id' => $portafolio->id,
                        'imagen_path' => $path,
                        'orden' => $orden++,
                    ]);
                }
            }

            return $portafolio;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Trabajo agregado al portafolio.']);

        return to_route('portafolio.index');
    }

    public function edit(Portafolio $portafolio): Response
    {
        $portafolio->load(['client', 'servicio', 'images']);

        return Inertia::render('portafolio/Edit', [
            'portafolio' => $this->toArray($portafolio),
            'categorias' => Portafolio::CATEGORIAS(),
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre']),
        ]);
    }

    public function update(PortafolioFormRequest $request, Portafolio $portafolio): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $portafolio, $data) {
            if ($request->hasFile('imagen_principal')) {
                if ($portafolio->imagen_principal) {
                    Storage::disk('public')->delete($portafolio->imagen_principal);
                }
                $data['imagen_principal'] = $request->file('imagen_principal')->store('portafolio', 'public');
            }

            $portafolio->update($data);

            if ($request->hasFile('imagenes_adicionales')) {
                $lastOrden = $portafolio->images()->count();
                foreach ($request->file('imagenes_adicionales') as $file) {
                    $path = $file->store('portafolio', 'public');
                    PortafolioImage::create([
                        'portafolio_id' => $portafolio->id,
                        'imagen_path' => $path,
                        'orden' => ++$lastOrden,
                    ]);
                }
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Trabajo actualizado.']);

        return to_route('portafolio.index');
    }

    public function pending(): Response
    {
        $portafolios = Portafolio::with(['client:id,name', 'servicio:id,nombre'])
            ->where('publicar', false)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => $this->toArray($p));

        return Inertia::render('portafolio/Pendientes', [
            'portafolios' => $portafolios,
        ]);
    }

    public function togglePublish(Portafolio $portafolio): RedirectResponse
    {
        $portafolio->update(['publicar' => ! $portafolio->publicar]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $portafolio->publicar ? 'Trabajo publicado en el portafolio.' : 'Trabajo ocultado del portafolio.',
        ]);

        return to_route('portafolio.index');
    }

    public function destroyImage(Portafolio $portafolio, PortafolioImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->imagen_path);
        $image->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Imagen eliminada.']);

        return to_route('portafolio.edit', [
            'portafolio' => $portafolio->id,
        ]);
    }

    public function destroy(Portafolio $portafolio): RedirectResponse
    {
        foreach ($portafolio->images as $image) {
            Storage::disk('public')->delete($image->imagen_path);
        }
        Storage::disk('public')->delete($portafolio->imagen_principal);
        $portafolio->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Trabajo eliminado del portafolio.']);

        return to_route('portafolio.index');
    }

    private function toArray(Portafolio $portafolio): array
    {
        return [
            'id' => $portafolio->id,
            'titulo' => $portafolio->titulo,
            'client' => $portafolio->client ? ['id' => $portafolio->client->id, 'name' => $portafolio->client->name] : null,
            'servicio' => $portafolio->servicio ? ['id' => $portafolio->servicio->id, 'nombre' => $portafolio->servicio->nombre] : null,
            'categoria' => $portafolio->categoria,
            'descripcion' => $portafolio->descripcion,
            'imagen_principal' => $portafolio->imagen_url,
            'fecha' => $portafolio->fecha?->toDateString(),
            'publicar' => $portafolio->publicar,
            'images' => $portafolio->images->map(fn ($i) => [
                'id' => $i->id,
                'url' => $i->url,
            ]),
            'created_at' => $portafolio->created_at?->toISOString(),
        ];
    }
}
