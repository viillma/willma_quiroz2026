<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioFormRequest;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Servicio::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->string('categoria'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        $sort = $request->input('orden', 'recientes');
        match ($sort) {
            'az' => $query->orderBy('nombre'),
            'precio_asc' => $query->orderBy('precio'),
            'precio_desc' => $query->orderByDesc('precio'),
            'antiguos' => $query->orderBy('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $servicios = $query->get()->map(fn ($s) => $this->toArray($s));

        $categorias = Servicio::query()->whereNotNull('categoria')->distinct()->pluck('categoria')->values()->all();
        $categorias = array_values(array_unique(array_merge(Servicio::CATEGORIAS(), $categorias)));

        return Inertia::render('servicios/Index', [
            'servicios' => $servicios,
            'categorias' => $categorias,
            'filters' => [
                'search' => $request->input('search') ?? '',
                'categoria' => $request->input('categoria') ?? '',
                'estado' => $request->input('estado') ?? '',
                'orden' => $sort,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('servicios/Create', [
            'categorias' => Servicio::CATEGORIAS(),
        ]);
    }

    public function store(ServicioFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $data['codigo'] = $data['codigo'] ?? $this->nextCode();

        Servicio::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Servicio creado correctamente.']);

        return to_route('servicios.index');
    }

    public function show(Servicio $servicio): Response
    {
        $servicio->loadCount('orders');

        return Inertia::render('servicios/Show', [
            'servicio' => $this->toArray($servicio),
            'total_pedidos' => $servicio->orders_count,
        ]);
    }

    public function edit(Servicio $servicio): Response
    {
        return Inertia::render('servicios/Edit', [
            'servicio' => $this->toArray($servicio),
            'categorias' => Servicio::CATEGORIAS(),
        ]);
    }

    public function update(ServicioFormRequest $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Servicio actualizado.']);

        return to_route('servicios.index');
    }

    public function toggle(Servicio $servicio): RedirectResponse
    {
        $servicio->update(['estado' => $servicio->estado === 'activo' ? 'inactivo' : 'activo']);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $servicio->estado === 'activo' ? 'Servicio activado.' : 'Servicio desactivado.',
        ]);

        return to_route('servicios.index');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }

        $servicio->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Servicio eliminado.']);

        return to_route('servicios.index');
    }

    private function toArray(Servicio $servicio): array
    {
        return [
            'id' => $servicio->id,
            'codigo' => $servicio->codigo,
            'nombre' => $servicio->nombre,
            'slug' => $servicio->slug,
            'categoria' => $servicio->categoria,
            'descripcion' => $servicio->descripcion,
            'precio' => (float) $servicio->precio,
            'costo_estimado' => $servicio->costo_estimado !== null ? (float) $servicio->costo_estimado : null,
            'unidad' => $servicio->unidad,
            'tiempo_estimado' => $servicio->tiempo_estimado,
            'imagen' => $servicio->imagen_url,
            'estado' => $servicio->estado,
            'publicado' => $servicio->publicado,
            'destacado' => $servicio->destacado,
            'orden' => $servicio->orden,
            'seo_title' => $servicio->seo_title,
            'seo_description' => $servicio->seo_description,
            'caracteristicas' => $servicio->caracteristicas ?? [],
            'incluye' => $servicio->incluye ?? [],
            'created_at' => $servicio->created_at?->toISOString(),
        ];
    }

    private function nextCode(): string
    {
        $last = Servicio::latest('id')->first();
        $next = $last ? intval(substr((string) $last->id, -4)) + 1 : 1;

        return 'SRV-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
