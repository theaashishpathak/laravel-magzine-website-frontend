<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Redirect users to the author dashboard after registration.
     */
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        $intended = $request->hasSession() ? $request->session()->pull('url.intended') : null;
        if (is_string($intended) && $intended !== '') {
            return redirect($intended);
        }

        return redirect()->route('dashboard.author');
    }
}
