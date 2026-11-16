<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function index(): Response
    {
        $files = collect(Storage::disk('public')->allFiles('media'))
            ->map(fn ($path) => [
                'path' => $path,
                'name' => basename($path),
                'url' => Storage::disk('public')->url($path),
                'size' => Storage::disk('public')->size($path),
                'modified' => Storage::disk('public')->lastModified($path),
            ])
            ->sortByDesc('modified')
            ->values()
            ->all();

        return Inertia::render('web/media/Index', [
            'images' => $files,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $request->file('imagen')->store('media', 'public');

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Imagen subida a la biblioteca.']);

        return back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['path' => ['required', 'string']]);

        if ($request->input('path')) {
            Storage::disk('public')->delete($request->input('path'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Imagen eliminada.']);

        return back();
    }
}
