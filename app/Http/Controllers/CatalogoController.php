<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogoFormRequest;
use App\Models\CatalogoDiseno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CatalogoController extends Controller
{
    public const CATEGORIAS = [
        'Logos',
        'Flyers',
        'Banners',
        'Redes Sociales',
        'Fotografía',
        'Edición',
        'Otros',
    ];

    public function index(): Response
    {
        $query = CatalogoDiseno::query();

        if ($request_estado = request('estado')) {
            $query->where('estado', $request_estado);
        }

        if ($request_search = request('search')) {
            $query->where(function ($q) use ($request_search) {
                $q->where('nombre', 'like', "%{$request_search}%")
                    ->orWhere('descripcion', 'like', "%{$request_search}%");
            });
        }

        if ($request_categoria = request('categoria')) {
            $query->where('categoria', $request_categoria);
        }

        $disenos = (clone $query)
            ->orderBy('orden')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($d) => $this->toArray($d));

        return Inertia::render('catalog/Admin', [
            'disenos' => $disenos,
            'categorias' => self::CATEGORIAS,
            'filters' => [
                'estado' => $request_estado ?? null,
                'search' => $request_search ?? null,
                'categoria' => $request_categoria ?? null,
            ],
        ]);
    }

    public function publico(): Response
    {
        $disenos = CatalogoDiseno::publicados()->where('estado', 'activo')
            ->orderBy('orden')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($d) => $this->toArray($d));

        return Inertia::render('catalog/Index', [
            'disenos' => $disenos,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    private function toArray(CatalogoDiseno $d): array
    {
        return [
            'id' => $d->id,
            'nombre' => $d->nombre,
            'categoria' => $d->categoria,
            'descripcion' => $d->descripcion,
            'precio' => (float) $d->precio,
            'precio_anterior' => $d->precio_anterior !== null ? (float) $d->precio_anterior : null,
            'estado' => $d->estado,
            'publicado' => $d->publicado,
            'destacado' => $d->destacado,
            'orden' => $d->orden,
            'imagen' => $d->imagen_url,
            'created_at' => $d->created_at?->toISOString(),
        ];
    }

    public function store(CatalogoFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('catalogo', 'public');
        }

        CatalogoDiseno::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Diseño agregado al catálogo.']);

        return back();
    }

    public function update(CatalogoFormRequest $request, CatalogoDiseno $catalogoDiseno): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($catalogoDiseno->imagen) {
                Storage::disk('public')->delete($catalogoDiseno->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('catalogo', 'public');
        } elseif (! $request->filled('imagen')) {
            $data['imagen'] = $catalogoDiseno->imagen;
        }

        $catalogoDiseno->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Diseño actualizado.']);

        return back();
    }

    public function destroy(CatalogoDiseno $catalogoDiseno): RedirectResponse
    {
        if ($catalogoDiseno->imagen) {
            Storage::disk('public')->delete($catalogoDiseno->imagen);
        }

        $catalogoDiseno->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Diseño eliminado del catálogo.']);

        return back();
    }
}
