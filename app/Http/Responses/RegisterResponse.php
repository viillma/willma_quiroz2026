<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Newly registered users start as "pendiente" and need an admin to approve them,
     * so we log them out and send them back to the login screen with a notice.
     */
    public function toResponse($request): Response
    {
        $message = 'Tu cuenta fue creada y está pendiente de aprobación por un administrador.';

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse(['message' => $message], 201)
            : redirect()->route('login')->with('status', $message);
    }
}
