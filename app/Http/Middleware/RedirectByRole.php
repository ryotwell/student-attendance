<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()){
            $user = auth()->user();

            if($user->role === 'GURU'){
                return redirect('/teacher-area');
            }

            if($user->role === 'ADMIN'){
                return redirect('/');
            }
        }
        return $next($request);
    }
}
