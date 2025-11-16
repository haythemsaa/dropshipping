<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isActive()) {
            if (auth()->user()->isPending()) {
                return redirect()->route('account.pending')
                    ->with('warning', 'Votre compte est en attente d\'approbation.');
            }

            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été suspendu. Contactez l\'administrateur.');
        }

        return $next($request);
    }
}
