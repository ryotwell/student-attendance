<?php

namespace App\Helpers;

class RoleRedirect
{
    public static function path($user): string
    {
        return match ($user->role) {
            'SUPERADMIN'   => route('superadmin.dashboard'),
            'ADMIN'   => route('dashboard'),
            'GURU'    => route('guru.dashboard'),
            'GURU_BK' => route('bk.dashboard'),
            default   => route('dashboard'),
        };
    }
}