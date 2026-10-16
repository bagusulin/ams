<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse([
                'two_factor' => false,
            ], 200);
        }

        // Login langsung menuju dashboard AMS.
        // Tidak memerlukan current team atau personal team.
        return redirect()->intended(route('dashboard'));
    }
}
