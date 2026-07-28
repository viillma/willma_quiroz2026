<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignFileFormRequest;
use App\Models\DesignFile;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DesignFileController extends Controller
{
    public function index(string $currentTeam, Order $order): Response
    {
        $order->load(['designFiles.uploader', 'client']);

        return Inertia::render('disenos/Index', [
            'order' => $order,
        ]);
    }

    public function store(DesignFileFormRequest $request, string $currentTeam, Order $order): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('designs/'.$order->id, 'public');

        $lastVersion = $order->designFiles()->max('version') ?? 0;

        DesignFile::create([
            'order_id' => $order->id,
            'version' => $lastVersion + 1,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'comments' => $request->input('comments'),
            'uploaded_by' => Auth::id(),
        ]);

        return to_route('disenos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }

    public function download(string $currentTeam, Order $order, DesignFile $designFile): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk('public')->download($designFile->file_path);
    }

    public function approve(string $currentTeam, Order $order, DesignFile $designFile): RedirectResponse
    {
        $order->designFiles()->update(['is_approved' => false]);
        $designFile->update(['is_approved' => true]);

        return to_route('disenos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }

    public function destroy(string $currentTeam, Order $order, DesignFile $designFile): RedirectResponse
    {
        Storage::disk('public')->delete($designFile->file_path);
        $designFile->delete();

        return to_route('disenos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }
}
