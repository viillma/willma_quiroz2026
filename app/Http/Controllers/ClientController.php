<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        $clients = Client::orderBy('created_at', 'desc')->get();

        return Inertia::render('clientes/Index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('clientes/Create');
    }

    public function store(ClientFormRequest $request, string $currentTeam): RedirectResponse
    {
        Client::create($request->validated());

        return to_route('clientes.index', [
            'current_team' => $currentTeam,
        ]);
    }

    public function edit(string $currentTeam, Client $client): Response
    {
        return Inertia::render('clientes/Edit', [
            'client' => $client,
        ]);
    }

    public function update(ClientFormRequest $request, string $currentTeam, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return to_route('clientes.index', [
            'current_team' => $currentTeam,
        ]);
    }

    public function destroy(string $currentTeam, Client $client): RedirectResponse
    {
        $client->delete();

        return to_route('clientes.index', [
            'current_team' => $currentTeam,
        ]);
    }
}
