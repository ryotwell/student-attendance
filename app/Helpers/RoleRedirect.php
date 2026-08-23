<?php

namespace App\Helpers;

class RoleRedirect
{
    public static function path($user): string
    {
        return match ($user->role) {
            'ADMIN'   => route('dashboard'),
            'GURU'    => route('guru.dashboard'),
            'GURU_BK' => route('bk.dashboard'),
            default   => route('dashboard'),
        };
    }
}