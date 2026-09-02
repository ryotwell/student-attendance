<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $redirectTo = match ($user->role) {
            'SUPERADMIN'   => route('superadmin.dashboard'),
            'ADMIN'    => route('dashboard'),
            'GURU'     => route('guru.dashboard'),
            'GURU_BK'  => route('bk.dashboard'),
            default    => route('dashboard'),
        };

        return redirect()->intended($redirectTo);
    }
}