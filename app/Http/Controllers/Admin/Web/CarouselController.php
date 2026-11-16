<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CarouselController extends Controller
{
    public function index(): Response
    {
        $slides = CarouselSlide::orderBy('orden')->orderBy('id')->get();

        $images = collect(Storage::disk('public')->allFiles('media'))
            ->map(fn ($path) => [
                'path' => $path,
                'name' => basename($path),
                'url' => Storage::disk('public')->url($path),
            ])
            ->sortByDesc(fn ($f) => Storage::disk('public')->lastModified($f['path']))
            ->values()
            ->all();

        return Inertia::render('web/carousel/Index', [
            'slides' => $slides,
            'images' => $images,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('carousel', 'public');
        } elseif ($request->filled('imagen_url')) {
            $data['imagen'] = $request->input('imagen_url');
        }

        $data['orden'] = (CarouselSlide::max('orden') ?? 0) + 1;
        $data['activo'] = $request->boolean('activo');

        CarouselSlide::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide creado correctamente.']);

        return back();
    }

    public function update(Request $request, CarouselSlide $carouselSlide): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('imagen')) {
            if ($carouselSlide->imagen && ! filter_var($carouselSlide->imagen, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($carouselSlide->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('carousel', 'public');
        } elseif ($request->filled('imagen_url')) {
            if ($carouselSlide->imagen && ! filter_var($carouselSlide->imagen, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($carouselSlide->imagen);
            }
            $data['imagen'] = $request->input('imagen_url');
        }

        $data['activo'] = $request->boolean('activo');

        $carouselSlide->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide actualizado correctamente.']);

        return back();
    }

    public function destroy(CarouselSlide $carouselSlide): RedirectResponse
    {
        if ($carouselSlide->imagen && ! filter_var($carouselSlide->imagen, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($carouselSlide->imagen);
        }

        $carouselSlide->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide eliminado.']);

        return back();
    }

    public function toggle(CarouselSlide $carouselSlide): RedirectResponse
    {
        $carouselSlide->update(['activo' => ! $carouselSlide->activo]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estado actualizado.']);

        return back();
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:carousel_slides,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            CarouselSlide::whereKey($id)->update(['orden' => $position]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Orden actualizado.']);

        return back();
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'boton_texto' => ['nullable', 'string', 'max:255'],
            'boton_enlace' => ['nullable', 'string', 'max:500'],
            'duracion' => ['required', 'integer', 'min:2', 'max:120'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'imagen_url' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
