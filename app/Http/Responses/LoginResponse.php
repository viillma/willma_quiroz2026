<?php

namespace App\Http\Responses;

use App\Actions\Teams\CreateTeam;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($user && ! $user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Esta cuenta ha sido desactivada. Contacta al administrador.',
            ]);
        }

        if ($user) {
            $user->syncRolesFromLegacyColumn();
            $user->ensureRolesHavePermissions();
        }

        $team = $user?->currentTeam ?? $user?->personalTeam();

        if (! $team) {
            $team = app(CreateTeam::class)->handle($user, $user->name."'s Team", isPersonal: true);
            $user->refresh();
        }

        URL::defaults(['current_team' => $team->slug]);

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended("/{$team->slug}".Fortify::redirects('login'));
    }
}
